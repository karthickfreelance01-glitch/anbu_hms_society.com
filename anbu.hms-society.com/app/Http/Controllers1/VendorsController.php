<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorsController extends Controller
{
    // Show vendor list
    public function index()
    {
        $vendors = DB::table('vendors')->orderBy('id', 'desc')->get();
        return view('masters.vendors', compact('vendors'));
    }

    // Store new vendor (AJAX)
    public function store(Request $request)
    {
        

        $id = DB::table('vendors')->insertGetId([
            'vendor_name' => $request->vendor_name,
            'phone'       => $request->phone,
            'email'       => $request->email,
            'contact_person' => $request->contact_person,
            'address'     => $request->address,
           
        ]);

        return response()->json(['success' => true, 'id' => $id], 200);
    }

    // Fetch vendor details for edit (AJAX)
    public function edit($id)
    {
        $vendor = DB::table('vendors')->where('id', $id)->first();
        return response()->json($vendor);
    }

    // Update vendor (AJAX)
    public function update(Request $request, $id)
    {
      

        DB::table('vendors')->where('id', $id)->update([
            'vendor_name' => $request->vendor_name,
            'phone'       => $request->phone,
            'email'       => $request->email,
            'contact_person' => $request->contact_person,
            'address'     => $request->address,
        ]);

        return response()->json(['success' => true], 200);
    }

    // Delete vendor
    public function destroy($id)
    {
        DB::table('vendors')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Vendor deleted successfully!');
    }
}
