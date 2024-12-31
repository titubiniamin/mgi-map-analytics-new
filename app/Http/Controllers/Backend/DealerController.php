<?php
declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Exports\DealersExport;
use App\Http\Controllers\Controller;
use App\Imports\DealersImport;
use App\Models\Dealer;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

// Use this import for the Request class

class DealerController extends Controller
{
    public function index(): Renderable
    {
//        dd('dealers');
        $this->checkAuthorization(auth()->user(), ['dealer.view']);
        $dealers = Dealer::all();
        return view('dealers.index', [
            'dealers' => $dealers
        ]);
    }

    public function allDealers()
    {
        $this->checkAuthorization(auth()->user(), ['dealer.view']);

//        Log::info('Fetching all dealers');
        $dealers = Dealer::all()->toArray();
//        Log::info($dealers);

        return $dealers;
    }


    public function getDealerList(Request $request)
    {
       $dealerView = $this->checkAuthorization(auth()->user(), ['dealer.view']);
        $items = Dealer::all();
        return DataTables::of($items)
            ->addIndexColumn() // Adds the index column

            ->addColumn('name', function ($item) {
                return $item->name;
            })

            ->addColumn('action', function ($data) use($dealerView) {
                $output = '';
                // Wrap all icons in a single div to keep them aligned
                $output .= '<div class="table-actions">';

                // View icon (pass item id to the modal)
                $output .= '<a href="javascript:void(0);" class="view-item" data-id="' . $data->id . '">
        <i class="ik ik-eye f-16 mr-15 text-blue"></i>
    </a>';

                // Edit icon (only if the user has permission to update)
                if ($dealerView) {
                    $output .= '<a href="' . url('user/' . $data->id) . '" >
            <i class="ik ik-edit f-16 mr-15 text-green"></i>
        </a>';
                }

                // Delete icon (only if the user has permission to delete)

                    $output .= '<a href="' . url('user/delete/' . $data->id) . '">
            <i class="ik ik-trash-2 f-16 text-red"></i>
        </a>';


                $output .= '</div>'; // Close the table-actions div

                return $output;
            })

            ->rawColumns(['action']) // Allow raw HTML for 'image' and 'action' columns
            ->make(true);
    }
    public function create(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['dealer.create']);
        return view('dealers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['dealer.create']);

        // Validate the request data and handle any validation errors automatically
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'zone' => 'nullable|string|max:255',
            'dealer_code' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'mobile' => 'nullable|string|max:15', // Adjust max length as needed
            'address' => 'nullable|string|max:255',
            'longitude' => 'nullable',
            'latitude' => 'nullable',
            'location' => 'nullable',
            'district' => 'nullable',
            'average_sales' => 'nullable|numeric',
            'market_size' => 'nullable|numeric',
            'market_share' => 'nullable',
            'competition_brand' => 'nullable',
        ]);
        // Create the dealer with validated data
        Dealer::create($validatedData);

        // Flash success message to the session
        session()->flash('success', 'Dealer has been created.');

        // Redirect to the index route for dealers
        return redirect()->back();
    }

    public function edit(int $id): Renderable|RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['dealer.edit']);

        $dealer = Dealer::findOrFail($id);
        return view('dealers.edit', [
            'dealer' => $dealer,
            'roles' => Role::all(),
        ]);

    }

    public function update(Request $request, int $id): RedirectResponse
    {
//        dd(request()->all());
        $this->checkAuthorization(auth()->user(), ['dealer.edit']);

        $dealer = Dealer::findOrFail($id);

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'zone' => 'nullable|string|max:255',
            'dealer_code' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:dealers,email,' . $dealer->id,//email
            'website' => 'nullable|url|max:255',
            'mobile' => 'nullable|string|max:15', // Adjust max length as needed
            'address' => 'nullable|string|max:255',
            'longitude' => 'nullable',
            'latitude' => 'nullable',
            'location' => 'nullable',
            'district' => 'nullable',
            'average_sales' => 'nullable|numeric',
            'market_size' => 'nullable|numeric',
            'market_share' => 'nullable',
            'competition_brand' => 'nullable',
        ]);
//        dd('update');
//        dd($validatedData);

        // Update the dealer with validated data
        $dealer->update($validatedData);

        session()->flash('success', 'Dealer has been updated.');
        return back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['dealer.delete']);

        $dealer = Dealer::findOrFail($id);
        $dealer->delete();

        session()->flash('success', 'Dealer has been deleted.');
        return redirect()->route('admin.dealers.index');
    }

    public function importShow(Request $request)
    {

//        dd($request->route()->getName());
        $dealer = Dealer::all(); // Use findOrFail to throw an error if not found

        // Return the view with the dealer data
        return view('backend.pages.dealers.excel-import', compact('dealer'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx|max:2048',
        ], [
            'file.required' => 'Please upload a file.',
            'file.mimes'    => 'The file must be an Excel file with .xls or .xlsx extension.',
            'file.max'      => 'The file size must not exceed 2MB.',
        ]);

        $import = new DealersImport;

        // Import the data
        Excel::import($import, $request->file('file'));

        // Check for errors
        if (!empty($import->errors)) {
            return redirect()->back()->with('error', implode('<br>', $import->errors));
        }

        return redirect()->back()->with('success', 'Dealers imported successfully.');
    }


    public function export()
    {
        return Excel::download(new DealersExport, 'dealers.xlsx');
    }



}
