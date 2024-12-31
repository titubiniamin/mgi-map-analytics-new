<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\ItemType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use DataTables;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(){
        return view('inventory.item.index');
    }

    public function create(Request $request){
        $this->checkAuthorization(auth()->user(), ['inventory_item_create']);
        $itemTypes = ItemType::pluck('name','id')->all();
        $brands = Brand::pluck('name', 'id')->all();
        return view('inventory.item.create',compact('itemTypes','brands'));
    }

    public function store(Request $request)
    {
//        dd($request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'brands' => 'nullable|array',
            'brands.*' => 'string',
            'item_type_id' => 'nullable|integer|exists:item_types,id',
            'item_image' => 'nullable|image|max:2048',
            'initial_quantity' => 'nullable|integer|min:0',
        ]);
//        dd($request->hasFile('item_image'), $request->file('item_image'));
        $imagePath = null;
        if ($request->hasFile('item_image')) {
            $imagePath = $request->file('item_image')->store('item_images', 'public');
            Log::info('Image uploaded to: ' . $imagePath);
        }

//        dd($imagePath);

        $item = Item::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'image' => $imagePath,
            'item_type_id' => $validated['item_type_id'],
        ]);
//dd($item->id);
        if (!empty($validated['brands'])) {
            $brandIds = Brand::whereIn('id', $validated['brands'])->pluck('id')->toArray();
            $item->brands()->sync($brandIds);
        }

        if (!empty($request->input('quantity'))) {
            ItemStock::create([
                'item_id' => $item->id,
                'quantity' => $request->input('quantity'),
                'note'=>'Initial quantity',
                'stock_type' => 'add',
                'created_by' => Auth::user()->id,
            ]);
        }

        return redirect()->route('inventory.item.index')->with('success', 'Item created successfully.');
    }
    public function getItemList(Request $request)
    {
        $items = Item::with('brands', 'itemType')
            ->withSum('itemStock as total_stock', 'quantity')
            ->get();
//        dd($items);
        $hasItemUpdate = Auth::user()->can('inventory_item_update');
        $hasItemDelete = Auth::user()->can('inventory_item_delete');
        return DataTables::of($items)
            ->addIndexColumn() // Adds the index column
            ->addColumn('image', function ($item) {
                if($item->image){
                    $imageUrl = asset('storage/' . $item->image);
                }else{
                    $imageUrl = asset('storage/blank-item.png');
                }
//                $imageUrl = asset('storage/' . $item->image); // Assuming images are stored in 'storage/app/public/'
                return '<img src="' . $imageUrl . '" alt="Image" width="50" height="50">';
            })

            ->addColumn('name', function ($item) {
                return $item->name;
            })
            ->addColumn('description', function ($item) {
                return $item->description;
            })
            ->addColumn('itemType', function ($item) {
                return $item->itemType->name;  // Ensure that itemType exists
            })
            ->addColumn('brands', function ($item) {
                // Check if the item has brands, then return the brand names
                return $item->brands->isNotEmpty() ? $item->brands->pluck('name')->implode(', ') : 'No Brands';
            })
            ->addColumn('total_stock', function ($item) {
                return $item->total_stock ?? 0; // Return 0 if total_stock is null
            })
            ->addColumn('action', function ($data) use($hasItemUpdate, $hasItemDelete) {
                $output = '';
                // Wrap all icons in a single div to keep them aligned
                $output .= '<div class="table-actions">';

                // View icon (pass item id to the modal)
                $output .= '<a href="javascript:void(0);" class="view-item" data-id="' . $data->id . '">
        <i class="ik ik-eye f-16 mr-15 text-blue"></i>
    </a>';

                // Edit icon (only if the user has permission to update)
                if ($hasItemUpdate) {
                    $output .= '<a href="' . url('user/' . $data->id) . '" >
            <i class="ik ik-edit f-16 mr-15 text-green"></i>
        </a>';
                }

                // Delete icon (only if the user has permission to delete)
                if ($hasItemDelete) {
                    $output .= '<a href="' . url('user/delete/' . $data->id) . '">
            <i class="ik ik-trash-2 f-16 text-red"></i>
        </a>';
                }

                $output .= '</div>'; // Close the table-actions div

                return $output;
            })

            ->rawColumns(['image', 'description', 'action']) // Allow raw HTML for 'image' and 'action' columns
            ->make(true);
    }

    public function update(Request $request){

    }

    public function show($id)
    {
        $item = Item::with('brands', 'itemType','itemStock')
            ->withSum('itemStock as total_stock', 'quantity')
            ->findOrFail($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        return response()->json($item);
    }

}
