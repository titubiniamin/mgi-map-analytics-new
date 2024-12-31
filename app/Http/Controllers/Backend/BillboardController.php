<?php
declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Exports\BillboardsExport;
use App\Http\Controllers\Controller;
use App\Imports\BillboardsImport;
use App\Imports\DealersImport;
use App\Models\Billboard;
use App\Models\Retailer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

// Use this import for the Request class

class BillboardController extends Controller
{
    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['billboard.view']);
        $billboards = Billboard::all();
        return view('backend.pages.billboards.index', [
            'billboards' => $billboards
        ]);
    }

    public function allBillboards()
    {
        $this->checkAuthorization(auth()->user(), ['billboard.view']);

//        Log::info('Fetching all billboards');
        $billboards = Billboard::all()->toArray();
//        Log::info($billboards);

        return $billboards;
    }


    public function create(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['billboard.create']);

        return view('backend.pages.billboards.create');
    }

    public function store(Request $request): RedirectResponse
    {
//        dd($request->all());
        $this->checkAuthorization(auth()->user(), ['billboard.create']);

        // Validate the request data and handle any validation errors automatically
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'size' => 'nullable',
            'type' => 'nullable',
            'brand' => 'nullable',
            'longitude' => 'nullable',
            'latitude' => 'nullable',
            'location' => 'nullable',
            'district' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            // Generate a unique name for the image
            $uniqueName = uniqid() . '_' . time() . '.' . $request->file('image')->getClientOriginalExtension();

            // Store the new image with the unique name
            $imagePath = $request->file('image')->storeAs('billboard_images', $uniqueName, 'public');
            $validatedData['image'] = $imagePath;
        }
        // Create the billboard with validated data
        Billboard::create($validatedData);


        // Flash success message to the session
        session()->flash('success', 'Billboard has been created.');

        // Redirect to the index route for billboards
        return redirect()->back();
    }

    public function edit(int $id): Renderable|RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['billboard.edit']);

        $billboard = Billboard::findOrFail($id);
//        dd($billboard);
        return view('backend.pages.billboards.edit', [
            'billboard' => $billboard,
            'roles' => Role::all(),
        ]);

    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['billboard.edit']);

        $billboard = Billboard::findOrFail($id);

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'size' => 'nullable',
            'type' => 'nullable',
            'brand' => 'nullable',
            'longitude' => 'nullable',
            'latitude' => 'nullable',
            'location' => 'nullable',
            'district' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        // Check if a new image has been uploaded
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($billboard->image) {
                Storage::delete('public/' . $billboard->image);
            }

            // Generate a unique name for the new image
            $uniqueName = uniqid() . '_' . time() . '.' . $request->file('image')->getClientOriginalExtension();

            // Store the new image and get its path
            $imagePath = $request->file('image')->storeAs('billboard_images', $uniqueName, 'public');

            // Add the new image path to the validated data
            $validatedData['image'] = $imagePath;
        }

        // Update the billboard with the validated data
        $billboard->update($validatedData);

        // Flash success message to the session
        session()->flash('success', 'Billboard has been updated.');

        return back();
    }


    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['billboard.delete']);

        $billboard = Billboard::findOrFail($id);
        $billboard->delete();

        session()->flash('success', 'Billboard has been deleted.');
        return redirect()->route('admin.billboards.index');
    }

    public function export()
    {
        return Excel::download(new BillboardsExport, 'billboards.xlsx');
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

        $import = new BillboardsImport;

        // Import the data from the Excel file
        Excel::import($import, $request->file('file'));

        // Check for errors after import
        if (!empty($import->errors)) {
            return redirect()->back()->with('error', implode('<br>', $import->errors));
        }

        return redirect()->back()->with('success', 'Billboards imported successfully.');
    }



    public function importShow(Request $request)
    {
        $billboard = Billboard::all(); // Use findOrFail to throw an error if not found

        // Return the view with the billboard data
        return view('backend.pages.billboards.excel-import', compact('billboard'));
    }


}
