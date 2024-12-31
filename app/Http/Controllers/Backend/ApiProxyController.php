<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiProxyController extends Controller
{
    private $apiBarikoi;

    public function __construct(){
        $this->apiBarikoi=env('BARIKOI_API_KEY');
    }
    public function fetchAutocomplete(Request $request)
    {
        // Extract the query parameter from the request
        $query = $request->input('q', 'barikoi'); // Default to 'barikoi' if 'q' is not provided

        // Construct the full URL with the API key and query
        $url = "https://barikoi.xyz/v2/api/search/autocomplete/place";
        $response = Http::get("{$url}?api_key={$this->apiBarikoi}&q={$query}");

        // Check if the request was successful
        if ($response->successful()) {
            return response()->json($response->json());
        }

        // Handle errors
        return response()->json([
            'error' => 'Failed to fetch data from Barikoi.'
        ], $response->status());
    }
    public function reverseGeocode(Request $request)
    {
        dd('hi');
        $longitude = $request->input('longitude');
        $latitude = $request->input('latitude');
$url="https://barikoi.xyz/v2/api/search/reverse/geocode";
        $response = Http::get("{$url}?longitude={$longitude}&latitude={$latitude}&district=true&post_code=true&country=true&sub_district=true&union=true&pauroshova=true&location_type=true&division=true&address=true&area=true&api_key={$this->apiBarikoi}");
        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Failed to fetch address.'], $response->status());
    }


}
