<?php

namespace App\Http\Controllers\Api;

use App\Models\Membership;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MembershipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Membership::get();

        return view('membership.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $membership = null;

        return view('membership.form', compact('membership'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'membership' => 'required|string|max:10',
            'benefit' => 'required|string|max:30',
            'discount' => 'nullable|numeric',
            'expiration_period' => 'nullable|numeric'
        ]);

        if ($validate->fails())
        {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        }

        Membership::create([
            'membership' => $request->membership,
            'benefit' => $request->benefit,
            'discount' => $request->discount,
            'expiration_period' => $request->expiration_period
        ]);

        return redirect()->route('membership.index')->with('success', 'Data created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
