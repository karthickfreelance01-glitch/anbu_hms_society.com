<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeathRegisterController extends Controller
{
    // Show all death records
    public function index()
    {
        $deaths  = DB::table('death_register')
            ->leftJoin('patients', 'death_register.patient_id', '=', 'patients.id')
            ->select(
                'death_register.*',
                'patients.name',
                'patients.ip_number'
            )
            ->orderByDesc('death_register.id')
            ->get();

        return view('death_register.index', compact('deaths'));
    }

    // Show create form
    public function create()
    {
         $patients = DB::table('patients')->where('discharge_status','Pending')->get();

        return view('death_register.create', compact('patients'));
    }

    // Store new death record
    public function store(Request $request)
    {
       
        $deathDatetime = date('Y-m-d H:i:s', strtotime($request->death_date . ' ' . $request->death_time));

        $data = [
            'patient_id' => $request->patient_id,
            'death_datetime' => $deathDatetime,
            'note' => $request->note,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($request->hasFile('report_attachment')) {
            $file = $request->file('report_attachment');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('uploads/death_reports'), $filename);
            $data['report_attachment'] = $filename;
        }

        DB::table('death_register')->insert($data);

        if ($request->ajax()) {
            return response()->json(['message' => 'Death record added successfully.']);
        }

        return redirect()->route('death_register.index')->with('success', 'Death record added successfully.');
    }

    // Show edit form
    public function edit($id)
    {
        $death = DB::table('death_register')
            ->join('patients', 'death_register.patient_id', '=', 'patients.id')
            ->select(
                'death_register.*',
                'patients.name',
                'patients.ip_number'
            )
            ->where('death_register.id', $id)
            ->first();

            $patients = DB::table('patients')->where('discharge_status','Pending')->get();

        return view('death_register.edit', compact('death', 'patients'));
    }

    // Update death record
    public function update(Request $request, $id)
    {
        $death = DB::table('death_register')->where('id', $id)->first();
        if (!$death) {
            return redirect()->back()->withErrors(['msg' => 'Record not found']);
        }

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'death_date' => 'required|date',
            'death_time' => 'required|string',
            'note' => 'nullable|string',
            'report_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $deathDatetime = date('Y-m-d H:i:s', strtotime($request->death_date . ' ' . $request->death_time));

        $data = [
            'patient_id' => $request->patient_id,
            'death_datetime' => $deathDatetime,
            'note' => $request->note,
            'updated_at' => now(),
        ];

        if ($request->hasFile('report_attachment')) {
            $file = $request->file('report_attachment');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('uploads/death_reports'), $filename);

            // Delete old file if exists
            if ($death->report_attachment && file_exists(public_path('uploads/death_reports/' . $death->report_attachment))) {
                unlink(public_path('uploads/death_reports/' . $death->report_attachment));
            }

            $data['report_attachment'] = $filename;
        }

        DB::table('death_register')->where('id', $id)->update($data);

        return redirect()->route('death_register.index')->with('success', 'Death record updated successfully.');
    }

    // Delete record
    public function destroy($id)
    {
        $record = DB::table('death_register')->where('id', $id)->first();

        if ($record) {
            if (!empty($record->report_attachment)) {
                $path = public_path('uploads/death_reports/' . $record->report_attachment);
                if (file_exists($path)) unlink($path);
            }

            DB::table('death_register')->where('id', $id)->delete();
        }

        return redirect()->back()->with('success', 'Record deleted successfully.');
    }
}
