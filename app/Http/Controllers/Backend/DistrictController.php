<?php

namespace App\Http\Controllers\Backend;

use App\Exports\DistrictsExport;
use App\Exports\HighwallsExport;
use App\Http\Controllers\Controller;
use App\Models\AllDistrict;
use App\Models\District;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkAuthorization(auth()->user(), ['district.view']);
        $districts = District::all();
        return view('backend.pages.district.index', [
            'districts' => $districts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $all_districts=AllDistrict::all();
        return view('backend.pages.district.create',['all_districts'=>$all_districts]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkAuthorization(auth()->user(), ['district.create']);
        $request->validate([
            'name' => 'required|string|max:255|unique:districts,name',
            'average_sales' => 'nullable|numeric',
            'market_size' => 'nullable|numeric',
            'market_share' => 'nullable',
            'competition_brand' => 'nullable',
            'total_outlets' => 'nullable|numeric',
            'own_outlets' => 'nullable|numeric',
            'coverage' => 'nullable|numeric',
            'location' => 'required|string',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'district' => 'required|string|unique:districts,district',
        ]);

        // Create the new District record
        District::create($request->all());

        // Flash success message to the session
        session()->flash('success', 'District has been created.');

        // Redirect back to the previous page or a specific route
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        dd('show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $district=District::find($id);
        $all_districts=AllDistrict::all();
        return view('backend.pages.district.edit', ['district'=>$district,'all_districts'=>$all_districts]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->checkAuthorization(auth()->user(), ['district.edit']);
        $district=District::findOrfail($id);
        $request->validate([
            'name' => 'required|string|max:255|unique:districts,name,' . $district->id,
            'average_sales' => 'nullable|numeric',
            'market_size' => 'nullable|numeric',
            'market_share' => 'nullable',
            'competition_brand' => 'nullable',
            'total_outlets' => 'nullable|numeric',
            'own_outlets' => 'nullable|numeric',
            'coverage' => 'nullable|numeric',
            'location' => 'required|string',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'district' => 'required|string|unique:districts,district,' . $district->id,
        ]);

        // Update the District record
        $update = $district->update($request->all());

        // Flash success message to the session
        session()->flash('success', 'District has been updated.');

        // Redirect back to the previous page or a specific route
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function export()
    {
        return Excel::download(new DistrictsExport(), 'districts.xlsx');
    }
}
