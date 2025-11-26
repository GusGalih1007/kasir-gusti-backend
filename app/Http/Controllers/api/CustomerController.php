<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Customers;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Customers::get();

        // return new ApiResource(status: 200, message: 'Success!', resource: $data);
        return view('customer.index', compact('data'));
    }

    public function create()
    {
        $membership = Membership::get();
        $customer = null;
        $provinces = Province::get();

        return view('customer.form', compact('customer', 'membership', 'provinces'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make(
            data: $request->all(),
            rules: [
                'first_name' => 'required|string|max:100',
                'last_name' => 'nullable|string|max:100',
                'provinsi' => 'required|numeric|exists:indonesia_provinces,id',
                'kota' => 'required|numeric|exists:indonesia_cities,id',
                'kecamatan' => 'required|numeric|exists:indonesia_districts,id',
                'desa' => 'required|numeric|exists:indonesia_villages,id',
                'alamat' => 'required|string',
                'phone' => 'required|numeric|max_digits:15',
                'email' => 'required|email',
                'membership' => 'nullable|exists:memberships,membership_id',
            ],
        );

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        }

        $isMembership = false;
        $membershipId = null;

        if (filled($request->membership)) {
            $isMembership = true;
            $membershipId = $request->membership;
        }

        $provinsi = Province::findOrFail($request->provinsi);
        $kota = City::findOrFail($request->kota);
        $kecamatan = District::findOrFail($request->kecamatan);
        $desa = Village::findOrFail($request->desa);

        $data = Customers::create(
            attributes: [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'province_code' => $provinsi->code,
                'city_code' => $kota->code,
                'district_code' => $kecamatan->code,
                'village_code' => $desa->code,
                'alamat' => $request->alamat,
                'phone' => $request->phone,
                'email' => $request->email,
                'is_member' => $isMembership,
                'membership_id' => $membershipId,
            ],
        );

        if ($request->ajax()) {
            return response()->json(
                [
                    'message' => 'Customer added successfully!',
                    'data' => $data->load('member'), // Load membership relation
                ],
                201,
            );
        }

        return redirect()->route('customer.index')->with('success', 'Data created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $customer = Customers::with(['userCreator', 'userUpdator', 'member', 'order'])
            ->findOrFail($id);

        return view('customer.show', compact('customer'));
    }

    public function edit($id)
    {
        $membership = Membership::get();
        $provinces = Province::get();
        $customer = Customers::with(['province', 'city', 'district', 'village'])->findOrFail($id);

        return view('customer.form', compact('membership', 'customer', 'provinces'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = Customers::findOrFail(id: $id);
        if ($data == null) {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $validate = Validator::make(
            data: $request->all(),
            rules: [
                'first_name' => 'required|string|max:100',
                'last_name' => 'nullable|string|max:100',
                'provinsi' => 'required|numeric|exists:indonesia_provinces,id',
                'kota' => 'required|numeric|exists:indonesia_cities,id',
                'kecamatan' => 'required|numeric|exists:indonesia_districts,id',
                'desa' => 'required|numeric|exists:indonesia_villages,id',
                'alamat' => 'required|string',
                'phone' => 'required|numeric|max_digits:15',
                'email' => 'required|email',
                'membership' => 'nullable',
            ],
        );

        $isMembership = false;

        if ($request->membership != 'regular' && filled($request->membership)) {
            $isMembership = true;
        }

        if ($validate->fails()) {
            return redirect()->back()->with('Data does not exist!');
            // return response()->json(data: $validate->errors(), status: 422);
        }

        $provinsi = Province::findOrFail($request->provinsi);
        $kota = City::findOrFail($request->kota);
        $kecamatan = District::findOrFail($request->kecamatan);
        $desa = Village::findOrFail($request->desa);

        $data->update(
            attributes: [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'province_code' => $provinsi->code,
                'city_code' => $kota->code,
                'district_code' => $kecamatan->code,
                'village_code' => $desa->code,
                'alamat' => $request->alamat,
                'phone' => $request->phone,
                'email' => $request->email,
                'is_member' => $isMembership,
                'membership_id' => $request->membership,
            ],
        );

        // return new ApiResource(status: 201, message: 'Data updated Successfully!', resource: $data);
        return redirect()->route('customer.index')->with('success', 'Data updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Customers::findOrFail(id: $id);

        if ($data == null) {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $data->delete();

        return redirect()->route('customer.index')->with('success', 'Data deleted successfully');
        // return new ApiResource(status: 204, message: 'Data deleted Successfully!', resource: null);
    }
}
