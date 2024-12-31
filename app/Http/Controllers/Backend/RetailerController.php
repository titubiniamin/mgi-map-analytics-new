<?php
declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Exports\RetailersExport;
use App\Http\Controllers\Controller;
use App\Imports\RetailersImport;
use App\Models\Retailer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

// Use this import for the Request class

class RetailerController extends Controller
{
    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['retailer.view']);
        $retailers = Retailer::all();
        return view('backend.pages.retailers.index', [
            'retailers' => $retailers
        ]);
    }

    public function allRetailers()
    {
        $this->checkAuthorization(auth()->user(), ['retailer.view']);

//        Log::info('Fetching all retailers');
        $retailers = Retailer::all()->toArray();
//        Log::info($retailers);

        return $retailers;
    }



    public function create(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['retailer.create']);

        return view('backend.pages.retailers.create');
    }

    public function store(Request $request): RedirectResponse
    {
//dd(request()->all());
        // Check authorization before proceeding
        $this->checkAuthorization(auth()->user(), ['retailer.create']);

        // Validate the request data and handle any validation errors automatically
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'zone' => 'nullable|string|max:255',
            'retailer_code' => 'nullable|string|max:255',
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
        // Create the retailer with validated data
        Retailer::create($validatedData);

        // Flash success message to the session
        session()->flash('success', 'Retailer has been created.');

        // Redirect to the index route for retailers
        return redirect()->back();
    }

    public function edit(int $id): Renderable|RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['retailer.edit']);

        $retailer = Retailer::findOrFail($id);
        return view('backend.pages.retailers.edit', [
            'retailer' => $retailer,
            'roles' => Role::all(),
        ]);

    }

    public function update(Request $request, int $id): RedirectResponse
    {
//        dd(request()->all());
        $this->checkAuthorization(auth()->user(), ['retailer.edit']);

        $retailer = Retailer::findOrFail($id);

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'zone' => 'nullable|string|max:255',
            'retailer_code' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:retailers,email,' . $retailer->id,//email
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

        // Update the retailer with validated data
        $retailer->update($validatedData);

        session()->flash('success', 'Retailer has been updated.');
        return back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['retailer.delete']);

        $retailer = Retailer::findOrFail($id);
        $retailer->delete();

        session()->flash('success', 'Retailer has been deleted.');
        return redirect()->route('admin.retailers.index');
    }

    public function importShow(Request $request)
    {

        $retailer = Retailer::all(); // Use findOrFail to throw an error if not found

        // Return the view with the retailer data
        return view('backend.pages.retailers.excel-import', compact('retailer'));
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

        $import = new RetailersImport();

        // Perform the import
        Excel::import($import, $request->file('file'));

        // Check for errors
        if (!empty($import->errors)) {
            return redirect()->back()->with('error', implode('<br>', $import->errors));
        }

        return redirect()->back()->with('success', 'Retailers imported successfully.');
    }



    public function export()
    {
        return Excel::download(new RetailersExport, 'retailers.xlsx');
    }



}
