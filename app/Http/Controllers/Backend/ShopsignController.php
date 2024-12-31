<?php
declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Exports\ShopsignsExport;
use App\Http\Controllers\Controller;
use App\Imports\ShopsignsImport;
use App\Models\Shopsign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

// Use this import for the Request class

class ShopsignController extends Controller
{
    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['shopsign.view']);
        $shopsigns = Shopsign::all();
        return view('backend.pages.shopsigns.index', [
            'shopsigns' => $shopsigns
        ]);
    }

    public function allShopsigns()
    {
        $this->checkAuthorization(auth()->user(), ['shopsign.view']);

//        Log::info('Fetching all shopsigns');
        $shopsigns = Shopsign::all()->toArray();
//        Log::info($shopsigns);

        return $shopsigns;
    }



    public function create(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['shopsign.create']);

        return view('backend.pages.shopsigns.create');
    }

    public function store(Request $request): RedirectResponse
    {

        $this->checkAuthorization(auth()->user(), ['shopsign.create']);

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
            'image'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);
        Log::info('Submitted Type: ' . $request->input('type'));
        if ($request->hasFile('image')) {
            // Generate a unique name for the image
            $uniqueName = uniqid() . '_' . time() . '.' . $request->file('image')->getClientOriginalExtension();

            // Store the new image with the unique name
            $imagePath = $request->file('image')->storeAs('shopsign_images', $uniqueName, 'public');
            $validatedData['image'] = $imagePath;
        }
        // Create the shopsign with validated data
        Shopsign::create($validatedData);

        // Flash success message to the session
        session()->flash('success', 'Shopsign has been created.');

        // Redirect to the index route for shopsigns
        return redirect()->back();
    }

    public function edit(int $id): Renderable|RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['shopsign.edit']);

        $shopsign = Shopsign::findOrFail($id);
        return view('backend.pages.shopsigns.edit', [
            'shopsign' => $shopsign,
            'roles' => Role::all(),
        ]);

    }

    public function update(Request $request, int $id): RedirectResponse
    {
//        dd(request()->all());
        $this->checkAuthorization(auth()->user(), ['shopsign.edit']);

        $shopsign = Shopsign::findOrFail($id);

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
//        dd('update');
//        dd($validatedData);
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($shopsign->image) {
                Storage::delete('public/' . $shopsign->image);
            }

            // Generate a unique name for the new image
            $uniqueName = uniqid() . '_' . time() . '.' . $request->file('image')->getClientOriginalExtension();

            // Store the new image and get its path
            $imagePath = $request->file('image')->storeAs('shopsign_images', $uniqueName, 'public');

            // Add the new image path to the validated data
            $validatedData['image'] = $imagePath;
        }

        // Update the shopsign with validated data
        $shopsign->update($validatedData);

        session()->flash('success', 'Shopsign has been updated.');
        return back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['shopsign.delete']);

        $shopsign = Shopsign::findOrFail($id);
        $shopsign->delete();

        session()->flash('success', 'Shopsign has been deleted.');
        return redirect()->route('admin.shopsigns.index');
    }

    public function importShow(Request $request)
    {

//        dd($request->route()->getName());
        $shopsign = Shopsign::all(); // Use findOrFail to throw an error if not found

        // Return the view with the shopsign data
        return view('backend.pages.shopsigns.excel-import', compact('shopsign'));
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

        $import = new ShopsignsImport;

        // Import the data from the Excel file
        Excel::import($import, $request->file('file'));

        // Check for errors after import
        if (!empty($import->errors)) {
            return redirect()->back()->with('error', implode('<br>', $import->errors));
        }

        return redirect()->back()->with('success', 'Shopsigns imported successfully.');
    }

    public function export()
    {
        return Excel::download(new ShopsignsExport, 'shopsigns.xlsx');
    }



}
