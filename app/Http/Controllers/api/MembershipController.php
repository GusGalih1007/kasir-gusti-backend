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
        // return response()->json(['data' => $data], 200);
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
            'expiration_period' => intval($request->expiration_period)
        ]);

        return redirect()->route('membership.index')->with('success', 'Data created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Membership::findOrFail($id);

        if(!$data)
        {
            return redirect()->back()->with('errors', 'Data does not exist!');
        }

        return;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $membership = Membership::findOrFail($id);

        if(!$membership)
        {
            return redirect()->back()->with('errors', 'Data does not exist!');
        }

        return view('membership.form', compact('membership'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = Membership::findOrFail($id);

        if(!$data)
        {
            return redirect()->back()->with('errors', 'Data does not exist!');
        }

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

        $data->update([
            'membership' => $request->membership,
            'benefit' => $request->benefit,
            'discount' => $request->discount,
            'expiration_period' => intval($request->expiration_period)
        ]);

        return redirect()->route('membership.index')->with('success', 'Data updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Membership::findOrFail($id);

        if(!$data)
        {
            return redirect()->back()->with('errors', 'Data does not exist!');
        }

        $data->delete();

        return redirect()->route('membership.index')->with('success', 'Data deleted successfully');
    }
}
