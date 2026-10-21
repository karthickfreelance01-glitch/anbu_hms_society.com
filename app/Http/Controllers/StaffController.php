<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function view()
    {
        $staff = DB::table('staff')->orderBy('id', 'desc')->get();
        return view('masters.staff', compact('staff'));
    }

    public function staff_add(Request $request)
    {

        DB::table('staff')->insert([
            'staff_name' => $request->staff_name,
            'contact_no' => $request->contact_no,
            'email' => $request->email,
            'role' => $request->role,
            'join_date' => $request->join_date,
            'address' => $request->address,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Staff added successfully.']);
    }

    public function staff_edit($id)
    {
        $staff = DB::table('staff')->where('id', $id)->first();
        return response()->json($staff);
    }

    public function staff_update(Request $request, $id)
    {
       

        DB::table('staff')->where('id', $id)->update([
            'staff_name' => $request->staff_name,
            'contact_no' => $request->contact_no,
            'email' => $request->email,
             'role' => $request->role,
            'join_date' => $request->join_date,
            'address' => $request->address,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Staff updated successfully.']);
    }

    public function staff_delete($id)
    {
        DB::table('staff')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Staff deleted successfully.');
    }
}
