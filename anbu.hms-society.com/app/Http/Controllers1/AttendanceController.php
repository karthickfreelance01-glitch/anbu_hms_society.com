<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // 1. Attendance Marking Page
    public function index() {
        $staff = DB::table('staff')->get();
        $today = Carbon::today()->toDateString();

        $attendance = DB::table('staff_attendance')
            ->where('attendance_date', $today)
            ->pluck('status','staff_id'); // staff_id => status

        return view('attendance.mark', compact('staff','attendance','today'));
    }

   public function bulkStore(Request $request)
    {
        $date = $request->attendance_date;
        $attendances = $request->attendance;

        foreach($attendances as $staff_id => $status){
            DB::table('staff_attendance')
                ->updateOrInsert(
                    ['staff_id'=>$staff_id,'attendance_date'=>$date],
                    ['status'=>$status]
                );
        }

        return redirect()->route('attendance.today')->with('success','Attendance saved successfully');
    }

    // 3. Today's Attendance Report
    public function todayReport() {
        $today = Carbon::today()->toDateString();
        $attendance = DB::table('staff_attendance')
            ->join('staff','staff_attendance.staff_id','=','staff.id')
            ->where('attendance_date', $today)
            ->select('staff.staff_name','staff_attendance.status')
            ->get();

        return view('attendance.today', compact('attendance','today'));
    }

    // 4. Attendance History
   public function history(Request $request) {
    $query = DB::table('staff_attendance')
        ->join('staff','staff_attendance.staff_id','=','staff.id')
        ->select('staff.staff_name','staff_attendance.status','staff_attendance.attendance_date');

    if ($request->filled('staff_id')) {
        $query->where('staff_id', $request->staff_id);
    }

    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('attendance_date', [
            $request->from_date,
            $request->to_date
        ]);
    }

    $attendance = $query->orderBy('attendance_date','desc')->get();
    $staff = DB::table('staff')->get(); // for staff filter dropdown

    return view('attendance.history', compact('attendance','staff'));
}


    // 5. Edit Attendance
    public function edit($id) {
        $record = DB::table('staff_attendance')->where('id',$id)->first();
        return view('attendance.edit', compact('record'));
    }

    // 6. Update Attendance
    public function update(Request $request, $id) {
        DB::table('staff_attendance')->where('id',$id)
            ->update([
                'status'=>$request->status
            ]);

        return redirect()->back()->with('success','Attendance updated successfully');
    }

    public function patientReport(Request $request)
{
    $staff = DB::table('staff')->get();

    $attendance = DB::table('staff_attendance')
        ->join('staff', 'staff_attendance.staff_id', '=', 'staff.id')
        ->select('staff.staff_name', 'staff_attendance.status')
        ->when($request->staff_id, fn($q) => $q->where('staff.id', $request->staff_id))
        ->get();

    // Count total per status per staff
    $totals = $attendance->groupBy('staff_name')->map(function($group){
        return [
            'Present' => $group->where('status','Present')->count(),
            'Absent' => $group->where('status','Absent')->count(),
            'Leave' => $group->where('status','Leave')->count(),
            'Half Day' => $group->where('status','Half Day')->count(),
        ];
    });

    return view('attendance.patient_report', compact('staff','attendance','totals'));
}

    public function dateRangeReport(Request $request)
{
    $from = $request->from_date;
    $to = $request->to_date;
    $staff_id = $request->staff_id;

    // Always fetch all staff for dropdown
    $allStaff = DB::table('staff')->orderBy('staff_name')->get();

    // Staff for table (only after filter)
    $staffQuery = DB::table('staff')->orderBy('staff_name');
    if ($staff_id) {
        $staffQuery->where('id', $staff_id);
    }

    $staff = collect();
    if (($from && $to) || $staff_id) {
        $staff = $staffQuery->get();
    }

    // Get all dates in range
    $dates = [];
    if ($from && $to) {
        $start = \Carbon\Carbon::parse($from);
        $end = \Carbon\Carbon::parse($to);
        for ($date = $start; $date->lte($end); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }
    }

    // Get attendance only if staff and dates exist
    $attendanceRecords = collect();
    if ($staff->isNotEmpty() && !empty($dates)) {
        $attendanceRecords = DB::table('staff_attendance')
            ->select('staff_id', 'attendance_date', 'status')
            ->when($from && $to, fn($q) => $q->whereBetween('attendance_date', [$from, $to]))
            ->when($staff_id, fn($q) => $q->where('staff_id', $staff_id))
            ->get()
            ->groupBy('staff_id')
            ->map(fn($att) => $att->keyBy('attendance_date'));
    }

    return view('attendance.date_report', compact(
        'allStaff',    // for dropdown
        'staff',       // for table
        'dates',
        'attendanceRecords',
        'from',
        'to',
        'staff_id'
    ));
}


}
