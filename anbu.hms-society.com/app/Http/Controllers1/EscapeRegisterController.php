<?php

// app/Http/Controllers/EscapeRegisterController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EscapeRegisterController extends Controller
{
   

public function index(Request $request)
{
   $escapes = DB::table('escape_registers')
        ->join('patients', 'escape_registers.patient_id', '=', 'patients.id')
        ->select('escape_registers.*')
        ->get();
    

      
    // Normal page load
    return view('escape.index', compact('escapes'));
}

public function reports(Request $request)
{
    $query = DB::table('escape_registers as e')
        ->leftJoin('patients as p', 'e.patient_id', '=', 'p.id')
        ->select(
            'e.*',
            'p.id as id',
            'p.name',
            'p.contact_number'
        );

    // Apply filters only if values exist
    if ($request->from_date) {
        $query->whereDate('e.escape_datetime', '>=', $request->from_date);
    }
    if ($request->to_date) {
        $query->whereDate('e.escape_datetime', '<=', $request->to_date);
    }
    if ($request->patient_id) {
        $query->where('p.patient_id', 'LIKE', "%{$request->patient_id}%");
    }
    if ($request->patient_name) {
        $query->where('p.full_name', 'LIKE', "%{$request->patient_name}%");
    }
    if ($request->contact_no) {
        $query->where('p.contact_number', 'LIKE', "%{$request->contact_no}%");
    }

    // Default: show today's records if no filters applied
    if (
        !$request->from_date &&
        !$request->to_date &&
        !$request->patient_id &&
        !$request->patient_name &&
        !$request->contact_no
    ) {
        $query->whereDate('e.escape_datetime', Carbon::today());
    }

    $escapes = $query->orderBy('e.id', 'desc')->get();

    if ($request->ajax()) {
        $html = '';
        if ($escapes->count()) {
            foreach ($escapes as $escape) {
                $video = $escape->video_footage ? '<a href="'.asset($escape->video_footage).'" target="_blank">View Video</a>' : 'N/A';
                $fir   = $escape->police_fir_copy ? '<a href="'.asset($escape->police_fir_copy).'" target="_blank">View FIR</a>' : 'N/A';
                $file  = $escape->file_attach ? '<a href="'.asset($escape->file_attach).'" target="_blank">View File</a>' : 'N/A';

                $html .= '<tr>
                    <td>'.e($escape->id).'</td>
                    <td>'.e($escape->patient_code ?? '-').'</td>
                    <td>'.e($escape->full_name ?? '-').'</td>
                    <td>'.e($escape->contact_number ?? '-').'</td>
                    <td>'.e(date('d-m-Y H:i', strtotime($escape->escape_datetime))).'</td>
                    <td>'.$video.'</td>
                    <td>'.$fir.'</td>
                    <td>'.$file.'</td>
                    <td>
                        <a href="'.route('escape.edit', $escape->id).'" class="btn btn-sm btn-warning">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <form id="delete-form-'.$escape->id.'" action="'.route('escape.destroy', $escape->id).'" method="POST" style="display:inline;">
                            '.csrf_field().method_field('DELETE').'
                            <button type="button" class="btn btn-sm btn-danger deleteEscapeBtn" data-id="'. $escape->id .'">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>';
            }
        } else {
            $html = '<tr><td colspan="9" class="text-center">No records found</td></tr>';
        }

        return response()->json(['html' => $html]);
    }

    return view('escape.reports', compact('escapes'));
}


    public function create() {
        $patients = DB::table('patients')->where('discharge_status','Pending')->get();
        return view('escape.create', compact('patients'));
    }

public function store(Request $request)
{
    // Validate request
    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'escape_date' => 'required|date',
        'escape_time' => 'required|string',
        'police_fir_copy' => 'required|file',
        'video_footage' => 'nullable|file',
        'file_attach' => 'nullable|file',
    ]);

    // Fetch patient details
    $patient = DB::table('patients')->where('id', $request->patient_id)->first();

    // Prepare data for insertion
    $data = [
        'patient_id' => $patient->id,
        'patient_name' => $patient->name,
        'escape_datetime' => $request->escape_date . ' ' . $request->escape_time,
        'created_at' => now(),
        'updated_at' => now(),
    ];

    // Upload files to respective folders
    if ($request->hasFile('video_footage')) {
        $file = $request->file('video_footage');
        $filename = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('escape/videos'), $filename);
        $data['video_footage'] = 'escape/videos/'.$filename;
    }

    if ($request->hasFile('police_fir_copy')) {
        $file = $request->file('police_fir_copy');
        $filename = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('escape/firs'), $filename);
        $data['police_fir_copy'] = 'escape/firs/'.$filename;
    }

    if ($request->hasFile('file_attach')) {
        $file = $request->file('file_attach');
        $filename = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('escape/files'), $filename);
        $data['file_attach'] = 'escape/files/'.$filename;
    }

    // Insert record
    DB::table('escape_registers')->insert($data);

    return redirect()->route('escape.index')->with('success', 'Escape registered successfully.');
}


    public function edit($id) {
        $escape = DB::table('escape_registers')->where('id', $id)->first();
        $patients = DB::table('patients')->where('discharge_status','Pending')->get();
        return view('escape.edit', compact('escape', 'patients'));
    }

   public function update(Request $request, $id)
{
    // ✅ Validate form input
    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'escape_date' => 'required|date',
        'escape_time' => 'required|string',
        'police_fir_copy' => 'nullable|file',
        'video_footage' => 'nullable|file',
        'file_attach' => 'nullable|file',
    ]);

    // ✅ Find existing record
    $escape = DB::table('escape_registers')->where('id', $id)->first();
    if (!$escape) {
        return redirect()->back()->withErrors(['msg' => 'Record not found']);
    }

    // ✅ Get patient info
    $patient = DB::table('patients')->where('id', $request->patient_id)->first();

    // ✅ Combine date and time into a single datetime
    $escapeDatetime = date('Y-m-d H:i:s', strtotime($request->escape_date . ' ' . $request->escape_time));

    // ✅ Prepare data for update
    $data = [
        'patient_id' => $patient->id,
        'patient_name' => $patient->name,
        'escape_datetime' => $escapeDatetime,
        'updated_at' => now(),
    ];

    // ✅ Upload video footage
    if ($request->hasFile('video_footage')) {
        $file = $request->file('video_footage');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('escape/videos'), $filename);
        $data['video_footage'] = 'escape/videos/' . $filename;
    }

    // ✅ Upload police FIR copy
    if ($request->hasFile('police_fir_copy')) {
        $file = $request->file('police_fir_copy');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('escape/firs'), $filename);
        $data['police_fir_copy'] = 'escape/firs/' . $filename;
    }

    // ✅ Upload additional file
    if ($request->hasFile('file_attach')) {
        $file = $request->file('file_attach');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('escape/files'), $filename);
        $data['file_attach'] = 'escape/files/' . $filename;
    }

    // ✅ Update record in DB
    DB::table('escape_registers')->where('id', $id)->update($data);

    // ✅ Redirect with success
    return redirect()->route('escape.index')->with('success', 'Escape record updated successfully.');
}

    public function destroy($id)
{
    $escape = DB::table('escape_registers')->where('id', $id)->first();

    if ($escape) {
        // Delete video footage if exists
        if (!empty($escape->video_footage) && file_exists(public_path($escape->video_footage))) {
            unlink(public_path($escape->video_footage));
        }

        // Delete police FIR copy if exists
        if (!empty($escape->police_fir_copy) && file_exists(public_path($escape->police_fir_copy))) {
            unlink(public_path($escape->police_fir_copy));
        }

        // Delete attached file if exists
        if (!empty($escape->file_attach) && file_exists(public_path($escape->file_attach))) {
            unlink(public_path($escape->file_attach));
        }

        // Delete record from database
        DB::table('escape_registers')->where('id', $id)->delete();
    }

    return redirect()->route('escape.index')->with('success', 'Escape record deleted successfully.');
}

}
