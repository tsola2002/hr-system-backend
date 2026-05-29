<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Customer::all();
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $id)
    {
        //
        return Customer::findOrFail($id);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $customer = Customer::create($request->all());
        return response()->json($customer, 201);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }




    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $customer = Customer::findOrFail($id);

        $customer->update($request->all());

        return response()->json($customer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        Customer::destroy($id);

        return response()->json([
            'message' => 'Customer deleted'
        ]);
    }
}
