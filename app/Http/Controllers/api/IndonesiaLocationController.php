<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;
use Maatwebsite\Excel\Files\Disk;

class IndonesiaLocationController extends Controller
{
    public function getProvinces()
    {
        $provinces = Province::get();

        return response()->json($provinces);
    }

    public function getCities(Request $request)
    {
        $province = Province::findOrFail($request->id);

        if (!$province)
        {
            return response()->json([]);
        }

        $cities = City::where('province_code', '=', $province->code)->get();

        return response()->json($cities);
    }

    public function getDistricts(Request $request)
    {
        $cities = City::findOrFail($request->id);

        if (!$cities)
        {
            return response()->json([]);
        }

        $districts = District::where('city_code', '=', $cities->code)->get();

        return response()->json($districts);
    }

    public function getVillages(Request $request)
    {
        $districts = District::findOrFail($request->id);

        if (!$districts)
        {
            return response()->json([]);
        }

        $village = Village::where('district_code', '=', $districts->code)->get();

        return response()->json($village);
    }
}
