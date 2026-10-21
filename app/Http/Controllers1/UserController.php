<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\User;
class UserController extends Controller
{
      // Roles
    public function roles(){

        $data['roles']=Db::table('roles')->get();

        return view('masters.roles', compact('data'));
    }  

    protected $roles = 'roles';

    public function roles_store(Request $request)
    {
        $request->validate([
            'roles_title' => 'required|string|max:255',
        ]);

    // Check if role already exists
    $exists = DB::table($this->roles)
                ->where('roles_title', $request->roles_title)
                ->exists();

    if ($exists) {
        // Return JSON error for AJAX
        return response()->json(['error' => 'Role already exists!'], 422);
    }

    // Insert new role
    $id = DB::table($this->roles)->insertGetId([
        'roles_title' => $request->roles_title,
    ]);

    // Return JSON success for AJAX
    return response()->json(['success' => 'Role added successfully!']);
}


    public function roles_edit($id)
    {
        $role = DB::table($this->roles)->where('id', $id)->first();
        return response()->json($role);
    }

    public function roles_update(Request $request, $id)
    {
        $request->validate([
            'roles_title' => 'required|string|max:255',
        ]);

        DB::table($this->roles)->where('id', $id)->update([
            'roles_title' => $request->roles_title,
            
        ]);

        return response()->json(['success' => true]);
    }


    // ✅ Delete role
    public function roles_delete($id)
    {
        DB::table($this->roles)->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Role deleted successfully!');
    }


  
    // Staff list
    public function staff() {
        $data['staff'] = DB::table('staff')
            ->join('roles','staff.role_id','=','roles.id')
            ->select('staff.*','roles.roles_title')
            ->get();
        $data['roles'] = DB::table('roles')->get();
        return view('masters.staff',compact('data'));
    }

    // Store new staff
public function staff_store(Request $request) {
   

    $photoName = time().'_photo.'.$request->staff_photo->extension();
    $request->staff_photo->move(public_path('uploads/staff/photos'), $photoName);

    $proofName = time().'_proof.'.$request->staff_proof->extension();
    $request->staff_proof->move(public_path('uploads/staff/proofs'), $proofName);

    DB::table('staff')->insert([
        'role_id'     => $request->roles_id,
        'staff_name'  => $request->staff_name,
        'contact_no'  => $request->contact_no,
        'email'       => $request->email,
        'salary'      => $request->salary,
        'joined_date' => $request->joined_date,
        'staff_photo' => $photoName,
        'staff_proof' => $proofName,
    ]);

    return response()->json(['success'=>true]);
}

// Update existing staff
public function staff_update(Request $request, $id) {
    $request->validate([
        'roles_id'    => 'required|exists:roles,id',
        'staff_name'  => 'required|string|max:255',
        'contact_no'  => 'required|digits:10',
        'email'       => 'required|email|max:255',
        'salary'      => 'required|numeric',
        'staff_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'staff_proof' => 'nullable|mimes:jpg,jpeg,png,pdf|max:4096',
        'joined_date' => 'required|date',
    ]);

    $updateData = [
        'role_id'    => $request->roles_id,
        'staff_name' => $request->staff_name,
        'contact_no' => $request->contact_no,
        'email'      => $request->email,
        'salary'     => $request->salary,
        'joined_date' => $request->joined_date,
    ];

    if ($request->hasFile('staff_photo')) {
        $photoName = time().'_photo.'.$request->staff_photo->extension();
        $request->staff_photo->move(public_path('uploads/staff/photos'), $photoName);
        $updateData['staff_photo'] = $photoName;
    }

    if ($request->hasFile('staff_proof')) {
        $proofName = time().'_proof.'.$request->staff_proof->extension();
        $request->staff_proof->move(public_path('uploads/staff/proofs'), $proofName);
        $updateData['staff_proof'] = $proofName;
    }

    DB::table('staff')->where('id', $id)->update($updateData);

    return response()->json(['success'=>true]);
}


    // Edit staff
   public function staff_edit($id)
{
    $staff = DB::table('staff')->where('id', $id)->first();

    if (!$staff) {
        return response()->json(['error' => 'Staff not found'], 404);
    }

    return response()->json([
        'id' => $staff->id,
        'role_id' => $staff->role_id,
        'staff_name' => $staff->staff_name,
        'contact_no' => $staff->contact_no,
        'email' => $staff->email,
        'salary' => $staff->salary,
        'joined_date' => $staff->joined_date,
        'staff_photo' => $staff->staff_photo,
        'staff_proof' => $staff->staff_proof
    ]);
}

     public function staff_view($id) {
    // Get the staff record
    $staff = DB::table('staff')
    ->join('roles','roles.id','staff.role_id')
    ->select('roles.roles_title','staff.*')
    ->where('staff.id', $id)->first();

    if (!$staff) {
        return redirect()->back()->with('error', 'Staff member not found.');
    }

    // Pass the staff data to the view
    return view('masters.staff_view', compact('staff'));
}



   
    // Delete staff
    public function staff_delete($id) {
        DB::table('staff')->where('id',$id)->delete();
        return redirect()->back()->with('success','Staff deleted!');
    }


    // Shift
    public function shift()
    {
        $data['shift']=Db::table('shift')->get();

        return view('masters.shift', compact('data'));
    }  

    protected $table = 'shifts'; // Your table name

    // Store Shift
    public function store(Request $request)
    {
        $request->validate([
            'shift_title' => 'required|string|max:255',
        ]);

        // Check for duplicate shift
        $exists = DB::table($this->table)
            ->where('shift_title', $request->shift_title)
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Shift already exists!'], 422);
        }

        DB::table($this->table)->insert([
            'shift_title' => $request->shift_title,
        ]);

        return response()->json(['success' => 'Shift added successfully!']);
    }

    public function shift_edit($id)
    {
        $shift = DB::table($this->shift)->where('id', $id)->first();
        return response()->json($shift);
    }

    public function shift_update(Request $request, $id)
    {
        $request->validate([
            'shift_title' => 'required|string|max:255',
        ]);

        DB::table($this->shift)->where('id', $id)->update([
            'shift_title' => $request->shift_title,
            
        ]);

        return response()->json(['success' => true]);
    }


    // ✅ Delete shift
    public function shift_delete($id)
    {
        DB::table($this->shift)->where('id', $id)->delete();

        return redirect()->back()->with('success', 'shift deleted successfully!');
    }


    // Ward
     public function ward()
    {
        $data['ward']=Db::table('ward')->get();

        return view('masters.ward', compact('data'));
    }  

    protected $ward = 'ward';
    public function ward_store(Request $request)
    {
        $request->validate([
            'ward_no' => 'required|string|max:255',
        ]);

        // Check if ward already exists
        $exists = DB::table($this->ward)
                    ->where('ward_no', $request->ward_no)
                    ->exists();

        if ($exists) {
            return response()->json([
                'errors' => ['ward_no' => ['Ward number already exists!']]
            ], 422); // 422 status for validation error
        }

        // Insert ward
        DB::table($this->ward)->insert([
            'ward_no' => $request->ward_no,
        ]);

        return response()->json(['success' => true]);
    }

    public function ward_edit($id)
    {
        $ward = DB::table($this->ward)->where('id', $id)->first();
        return response()->json($ward);
    }
    public function ward_update(Request $request, $id)
    {
        $request->validate([
            'ward_no' => 'required|string|max:255',
        ]);

        // Check if ward already exists excluding current id
        $exists = DB::table($this->ward)
                    ->where('ward_no', $request->ward_no)
                    ->where('id', '!=', $id)
                    ->exists();

        if ($exists) {
            return response()->json([
                'errors' => ['ward_no' => ['Ward number already exists!']]
            ], 422);
        }

        DB::table($this->ward)->where('id', $id)->update([
            'ward_no' => $request->ward_no,
        ]);

        return response()->json(['success' => true]);
    }

    // ✅ Delete shift
    public function ward_delete($id)
    {
        DB::table($this->ward)->where('id', $id)->delete();

        return redirect()->back()->with('success', 'ward deleted successfully!');
    }

     // ====== Symptoms ======
    public function symptomsIndex(){
        $data['symptoms'] = DB::table('symptoms')->get();
        return view('masters.symptoms', compact('data'));
    }

   // Store Symptom/Disease
public function symptomsStore(Request $request)
{
    $request->validate([
        'symptoms_title' => 'required|string|max:255',
    ]);

    // Check if symptom already exists
    $exists = DB::table('symptoms')
                ->where('symptoms_title', $request->symptoms_title)
                ->exists();

    if ($exists) {
        return response()->json([
            'errors' => ['symptoms_title' => ['Disease already exists!']]
        ], 422); // 422 status for validation error
    }

    // Insert symptom
    DB::table('symptoms')->insert([
        'symptoms_title' => $request->symptoms_title,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return response()->json(['success' => true]);
}

// Update Symptom/Disease
public function symptomsUpdate(Request $request, $id)
{
    $request->validate([
        'symptoms_title' => 'required|string|max:255',
    ]);

    // Check if symptom already exists excluding current ID
    $exists = DB::table('symptoms')
                ->where('symptoms_title', $request->symptoms_title)
                ->where('id', '!=', $id)
                ->exists();

    if ($exists) {
        return response()->json([
            'errors' => ['symptoms_title' => ['Disease already exists!']]
        ], 422);
    }

    // Update symptom
    DB::table('symptoms')->where('id', $id)->update([
        'symptoms_title' => $request->symptoms_title,
        'updated_at' => now()
    ]);

    return response()->json(['success' => true]);
}


    public function symptomsEdit($id){
        $symptom = DB::table('symptoms')->find($id);
        return response()->json($symptom);
    }

   
    public function symptomsDelete($id){
        DB::table('symptoms')->where('id', $id)->delete();
        return redirect()->back()->with('success','Symptom deleted!');
    }

    // ====== Therapy ======
    public function therapyIndex(){
        $data['therapy'] = DB::table('therapy')->get();
        return view('masters.therapy', compact('data'));
    }

   public function therapyStore(Request $request){
            $request->validate([
                'therapy_name'=>'required|unique:therapy,therapy_name'
            ]);

            DB::table('therapy')->insert([
                'therapy_name'=>$request->therapy_name,
                'created_at'=>now(),
                'updated_at'=>now()
            ]);

            return response()->json(['success'=>true]);
        }

    public function therapyUpdate(Request $request, $id){
        $request->validate([
            'therapy_name'=>'required|unique:therapy,therapy_name,'.$id
        ]);

        DB::table('therapy')->where('id',$id)->update([
            'therapy_name'=>$request->therapy_name,
            'updated_at'=>now()
        ]);

        return response()->json(['success'=>true]);
    }


    public function therapyEdit($id){
        $therapy = DB::table('therapy')->find($id);
        return response()->json($therapy);
    }

  

    public function therapyDelete($id){
        DB::table('therapy')->where('id',$id)->delete();
        return redirect()->back()->with('success','Therapy deleted!');
    }

    // ====== Medicine ======
    public function medicineIndex(){
        $data['medicine'] = DB::table('medicine')->get();
        return view('masters.medicine', compact('data'));
    }

    public function addmedicine(){

        return view('masters.medicine_add');
    }

    public function medicineStore(Request $request)
{
    $request->validate([
        'medicine_name' => 'required',
        'medicine_dosage' => 'required',
        'medicine_brand' => 'required',
        'medicine_disease' => 'required',
        'medicine_type' => 'required',
    ]);

    DB::table('medicine')->insert([
        'medicine_name'   => $request->medicine_name,
        'medicine_dosage' => $request->medicine_dosage,
        'medicine_brand'  => $request->medicine_brand,
        'medicine_disease'=> $request->medicine_disease,
        'medicine_type'   => $request->medicine_type,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    return redirect()->back()->with('success', 'Medicine added!');
}


    public function medicineEdit($id){
        $medicine = DB::table('medicine')->find($id);
        return response()->json($medicine);
    }

   public function medicineUpdate(Request $request, $id)
{
    $request->validate([
        'medicine_name'   => 'required',
        'medicine_dosage' => 'required',
        'medicine_brand'  => 'required',
        'medicine_disease'=> 'required',
        'medicine_type'   => 'required',
    ]);

    DB::table('medicine')->where('id', $id)->update([
        'medicine_name'    => $request->medicine_name,
        'medicine_dosage'  => $request->medicine_dosage,
        'medicine_brand'   => $request->medicine_brand,
        'medicine_disease' => $request->medicine_disease,
        'medicine_type'    => $request->medicine_type,
        'updated_at'       => now(),
    ]);

    return response()->json(['success' => true, 'message' => 'Medicine updated successfully!']);
}


    public function medicineDelete($id){
        DB::table('medicine')->where('id',$id)->delete();
        return redirect()->back()->with('success','Medicine deleted!');
    }




}
