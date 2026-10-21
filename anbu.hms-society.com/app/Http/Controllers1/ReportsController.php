<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    // Show report upload form
    public function create()
    {
        $patients = DB::table('patients')->orderBy('name')->get();
        return view('reports.create', compact('patients'));
    }

    // Store multiple uploaded files
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required',
            'report_date' => 'required|date',
            'document_name.*' => 'required|string|max:255',
            'document_file.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120'
        ]);

        if ($request->hasFile('document_file')) {
            foreach ($request->file('document_file') as $index => $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/reports'), $filename);

                DB::table('reports')->insert([
                    'patient_id' => $request->patient_id,
                    'report_date' => $request->report_date,
                    'document_name' => $request->document_name[$index],
                    'document_file' => $filename,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        return redirect()->route('reports.index')->with('success', 'Reports uploaded successfully.');
    }

    // List all uploaded reports
    public function index()
    {
        $reports = DB::table('reports')
            ->join('patients', 'reports.patient_id', '=', 'patients.id')
            ->select('reports.*', 'patients.name as patient_name')
            ->orderBy('reports.report_date', 'desc')
            ->get();

        return view('reports.index', compact('reports'));
    }

    // Edit a patient's reports
    public function edit($id)
    {
        $document = DB::table('reports')->where('patient_id', $id)->get();
        $patients = DB::table('patients')->orderBy('name')->get();

        if ($document->isEmpty()) {
            return redirect()->back()->with('error', 'No reports found for this patient.');
        }

        $patient_id = $document->first()->patient_id;
        $report_date = $document->first()->report_date;

        return view('reports.edit', compact('document', 'patients', 'patient_id', 'report_date'));
    }

    // Update reports
    public function update(Request $request, $id)
    {
        $request->validate([
            'patient_id' => 'required',
            'report_date' => 'required|date',
            'report_name.*' => 'required|string|max:255',
            'report_file.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120'
        ]);

        // Delete existing reports for the patient before re-inserting updated ones
        DB::table('reports')->where('patient_id', $id)->delete();

        // Re-insert all reports
        if ($request->has('report_name')) {
            foreach ($request->report_name as $index => $name) {
                $filename = null;
                if (isset($request->report_file[$index])) {
                    $file = $request->file('report_file')[$index];
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/reports'), $filename);
                } elseif (isset($request->existing_file[$index])) {
                    $filename = $request->existing_file[$index]; // Keep old file
                }

                DB::table('reports')->insert([
                    'patient_id' => $request->patient_id,
                    'report_date' => $request->report_date,
                    'document_name' => $name,
                    'document_file' => $filename,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        return redirect()->route('reports.index')->with('success', 'Reports updated successfully.');
    }

    // Delete a single report
    public function destroy($id)
    {
        $report = DB::table('reports')->where('id', $id)->first();
        if ($report) {
            $path = public_path('uploads/reports/' . $report->document_file);
            if (file_exists($path)) unlink($path);
            DB::table('reports')->where('id', $id)->delete();
        }
        return redirect()->back()->with('success', 'Report deleted successfully.');
    }
}
