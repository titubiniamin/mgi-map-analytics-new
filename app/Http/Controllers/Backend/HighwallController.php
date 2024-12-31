<?php
declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Exports\HighwallsExport;
use App\Http\Controllers\Controller;
use App\Imports\BillboardsImport;
use App\Imports\HighwallsImport;
use App\Models\Highwall;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

// Use this import for the Request class

class HighwallController extends Controller
{
    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['highwall.view']);
        $highwalls = Highwall::all();
        return view('backend.pages.highwalls.index', [
            'highwalls' => $highwalls
        ]);
    }

    public function allHighwalls()
    {
        $this->checkAuthorization(auth()->user(), ['highwall.view']);

//        Log::info('Fetching all highwalls');
        $highwalls = Highwall::all()->toArray();
//        Log::info($highwalls);

        return $highwalls;
    }



    public function create(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['highwall.create']);

        return view('backend.pages.highwalls.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['highwall.create']);

        // Validate the request data and handle any validation errors automatically
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
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

        if ($request->hasFile('image')) {
            // Generate a unique name for the image
            $uniqueName = uniqid() . '_' . time() . '.' . $request->file('image')->getClientOriginalExtension();

            // Store the new image with the unique name
            $imagePath = $request->file('image')->storeAs('highwall_images', $uniqueName, 'public');
            $validatedData['image'] = $imagePath;
        }
        // Create the highwall with validated data
        Highwall::create($validatedData);

        // Flash success message to the session
        session()->flash('success', 'Highwall has been created.');

        // Redirect to the index route for highwalls
        return redirect()->back();
    }

    public function edit(int $id): Renderable|RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['highwall.edit']);

        $highwall = Highwall::findOrFail($id);
        return view('backend.pages.highwalls.edit', [
            'highwall' => $highwall,
            'roles' => Role::all(),
        ]);

    }

    public function update(Request $request, int $id): RedirectResponse
    {
//        dd(request()->all());
        $this->checkAuthorization(auth()->user(), ['highwall.edit']);

        $highwall = Highwall::findOrFail($id);

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
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
            if ($highwall->image) {
                Storage::delete('public/' . $highwall->image);
            }

            // Generate a unique name for the new image
            $uniqueName = uniqid() . '_' . time() . '.' . $request->file('image')->getClientOriginalExtension();

            // Store the new image and get its path
            $imagePath = $request->file('image')->storeAs('billboard_images', $uniqueName, 'public');

            // Add the new image path to the validated data
            $validatedData['image'] = $imagePath;
        }
        // Update the highwall with validated data
        $highwall->update($validatedData);

        session()->flash('success', 'Highwall has been updated.');
        return back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['highwall.delete']);

        $highwall = Highwall::findOrFail($id);
        $highwall->delete();

        session()->flash('success', 'Highwall has been deleted.');
        return redirect()->route('admin.highwalls.index');
    }

    public function importShow(Request $request)
    {

//        dd($request->route()->getName());
        $highwall = Highwall::all(); // Use findOrFail to throw an error if not found

        // Return the view with the highwall data
        return view('backend.pages.highwalls.excel-import', compact('highwall'));
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

        $import = new HighwallsImport;

        // Import the data from the Excel file
        Excel::import($import, $request->file('file'));

        // Check for errors after import
        if (!empty($import->errors)) {
            return redirect()->back()->with('error', implode('<br>', $import->errors));
        }

        return redirect()->back()->with('success', 'Highwalls imported successfully.');
    }

    public function export()
    {
        return Excel::download(new HighwallsExport, 'highwalls.xlsx');
    }



}
