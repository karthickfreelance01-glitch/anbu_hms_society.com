<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ExpensesmasterController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DischargeController;
use App\Http\Controllers\DoctornotesController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\InjuryController;
use App\Http\Controllers\DangerouslyIllController;
use App\Http\Controllers\MedicalEquipmentController;
use App\Http\Controllers\PatientHearingController;
use App\Http\Controllers\CounsellornotesController;

use App\Http\Controllers\PatientController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\MedicineCategoryController;
use App\Http\Controllers\PaymentCategoryController;
use App\Http\Controllers\DeathRegisterController;
use App\Http\Controllers\EscapeRegisterController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\NewExpensesController;

use App\Http\Controllers\MedicineIssueController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\EmergencyController;
use App\Http\Controllers\EmergencyCallController;
use App\Http\Controllers\PatientexpensesController;

// Public routes (no middleware)
Route::get('/', [LoginController::class, 'login_form']);
Route::get('register_form', [LoginController::class, 'register_form'])->name('register');
Route::post('/login', [LoginController::class, 'login']);
Route::post('register', [LoginController::class, 'register']);

// Group all protected routes under middleware
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [LoginController::class, 'dashboard']);

    
    // Expense Masters
    Route::post('addexpenses', [ExpensesmasterController::class, 'addexpenses'])->name('addexpenses');
    Route::get('expenses/view', [ExpensesmasterController::class, 'view'])->name('expenses.view');
    Route::get('expenses_edit/{id}', [ExpensesmasterController::class, 'expenses_edit'])->name('editexpenses');
    Route::post('expenses_update/{id}', [ExpensesmasterController::class, 'expenses_update'])->name('updateexpenses');
    Route::get('expenses_delete/{id}', [ExpensesmasterController::class, 'expenses_delete'])->name('deleteexpenses');

    // Staff
    Route::get('staff/view', [StaffController::class, 'view']);
    Route::post('staff_add', [StaffController::class, 'staff_add']);
    Route::get('staff_edit/{id}', [StaffController::class, 'staff_edit']);
    Route::post('staff_update/{id}', [StaffController::class, 'staff_update']);
    Route::get('staff_delete/{id}', [StaffController::class, 'staff_delete']);

    Route::get('medicine', [MedicineController::class, 'index'])->name('medicine.index');
    Route::post('medicine/add', [MedicineController::class, 'store'])->name('medicine.add');
    Route::get('medicine/edit/{id}', [MedicineController::class, 'edit'])->name('medicine.edit');
    Route::post('medicine/update/{id}', [MedicineController::class, 'update'])->name('medicine.update');
    Route::get('medicine/delete/{id}', [MedicineController::class, 'destroy'])->name('medicine.delete');

   // Staff Attendance
    Route::get('attendance', [AttendanceController::class,'index']); 
    Route::post('attendance/save', [AttendanceController::class,'bulkStore'])->name('attendance.bulkStore'); 
    Route::get('attendance/today', [AttendanceController::class,'todayReport'])->name('attendance.today'); 
    Route::get('attendance/history', [AttendanceController::class,'history']); 
    Route::get('attendance/edit/{id}', [AttendanceController::class,'edit']); 
    Route::post('attendance/update/{id}', [AttendanceController::class,'update']); 
    Route::get('attendance/patient-report', [AttendanceController::class, 'patientReport'])->name('attendance.patientReport');
    Route::post('attendance/update-status', [AttendanceController::class, 'updateAttendanceStatus']);
    // Show attendance marking page for ANY date
    Route::get('attendance/mark-date', [AttendanceController::class, 'markByDate'])
         ->name('attendance.mark.date');
    
    // Save attendance for selected date
    Route::post('attendance/mark-date/save', [AttendanceController::class, 'markByDateStore'])
         ->name('attendance.mark.date.save');

// Date range attendance report
Route::get('attendance/date-report', [AttendanceController::class, 'dateRangeReport'])->name('attendance.dateReport');

    // Vendor Master Routes
    Route::get('vendors', [VendorsController::class, 'index'])->name('vendors');        
    Route::post('vendors/store', [VendorsController::class, 'store'])->name('addvendors'); 
    Route::get('vendors_edit/{id}', [VendorsController::class, 'edit']);                   
    Route::post('vendors_update/{id}', [VendorsController::class, 'update']);              
    Route::get('vendors_delete/{id}', [VendorsController::class, 'destroy']);              
    
   

    Route::get('payment-categories', [PaymentCategoryController::class, 'index'])->name('payment_categories');
    Route::post('payment-categories/store', [PaymentCategoryController::class, 'store'])->name('payment_categories.store');
    Route::get('payment-categories/edit/{id}', [PaymentCategoryController::class, 'edit'])->name('payment_categories.edit');
    Route::post('payment-categories/update/{id}', [PaymentCategoryController::class, 'update'])->name('payment_categories.update');
    Route::get('payment-categories/delete/{id}', [PaymentCategoryController::class, 'destroy'])->name('payment_categories.delete');
    Route::get('accounttype/reports', [PaymentCategoryController::class, 'accountTypeReports'])->name('accounttype.reports');


    // routes/web.php
    Route::post('billing/add-monthly', [PatientController::class, 'addMonthlyBill']);
    Route::get('patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('patients/{id}/discharge', [PatientController::class, 'discharge']);
    Route::post('patients/discharge/complete/{id}', [PatientController::class, 'dischargeComplete']);
    Route::get('patients/reports', [PatientController::class, 'reports'])->name('patients.reports');
    Route::get('patient/reports/{id}', [PatientController::class, 'getPatientDetails']);
    Route::get('patients/create', [PatientController::class, 'create'])->name('patients.create');
    Route::post('patients/store', [PatientController::class, 'store'])->name('patients.store');
    Route::get('patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
    Route::post('patients/{patient}/update', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
    Route::get('patients/{id}', [PatientController::class, 'show'])->name('patients.show');
    Route::get('addmission_form', [PatientController::class, 'addmission_form'])->name('patients.addmission_form');
    Route::get('admissions/get-details/{id}', [PatientController::class, 'getDetails']);
    Route::get('patients/{id}/discharge/view', [PatientController::class, 'dischargeView'])->name('patients.discharge.view');
    Route::get('patients/discharge/index', [PatientController::class, 'dischargeindex'])->name('patients.discharge.index');
    Route::get('patient/pending-reports', [PatientController::class, 'pending_reports'])->name('patient.pending.reports');
    Route::post('billing/add-payment', [PatientController::class, 'addPayment']);
    Route::get('patient_bills_report', [PatientController::class, 'patientbillreports']);
    Route::get('discharge', [PatientController::class, 'dischargeSelect'])->name('patients.discharge.select');
    Route::get('discharge/edit/{id}', [DischargeController::class, 'dischargeedit'])->name('patients.discharge.edit');
    Route::get('patient/discharge/pdf/{id}', [PatientController::class, 'dischargePdf'])->name('patient.discharge.pdf');
    Route::post('discharge/update/{id}', [DischargeController::class, 'dischargeupdate'])->name('patients.discharge.update');
    Route::post('patient/recalculate-bill', [DischargeController::class, 'recalculateBill'])
    ->name('patient.recalculate.bill');
    Route::post('store-discharge-date', [DischargeController::class, 'storeDischargeDate'])
    ->name('patient.discharge.storeDate');

    Route::get('patient/billing', [BillingController::class, 'create'])->name('patient.billing.create');
    Route::post('patient/billing', [BillingController::class, 'store'])->name('patient.billing.store');
    Route::get('patient/billing/reports', [BillingController::class, 'patientbillreports'])->name('patient.billing.reports');
    
    // Billing Details
    Route::get('patient-billing-details/{id}', [BillingController::class, 'getPatientBillingDetails']);
    Route::get('patient-billing-item/{id}', [BillingController::class, 'getBillingItem']);
    Route::post('update-patient-billing-item/{id}', [BillingController::class, 'updateBillingItem']);

    Route::delete('delete-patient-billing-item/{id}', [BillingController::class, 'deleteBillingItem']);
    Route::post('delete-payment/{id}', [BillingController::class, 'deleteBillingPayment']);

    
    // Pending + Payment
    Route::get('patient-pending-categories/{id}', [BillingController::class, 'getPendingCategories']);
    Route::get('patient-pending-category-by-id/{id}', [BillingController::class, 'getPendingByBillingId']);

    Route::post('add-patient-payment', [BillingController::class, 'addPayment']);
    
    // Print View
    Route::get('patient/bill/{bill_id}', [BillingController::class, 'viewBill']);
    Route::get('income/reports', [BillingController::class, 'reports'])->name('income.reports');
    Route::middleware('finance.role')->group(function () {
        Route::get('income/create', [IncomeController::class, 'create'])->name('income.create');
        Route::post('income/store', [IncomeController::class, 'store'])->name('income.store');
        Route::get('income/ledger-reports', [IncomeController::class, 'reports'])->name('income.ledger.reports');
        Route::get('income/balance-report', [IncomeController::class, 'balanceReport'])->name('income.balance-report');
        Route::get('income/{id}/edit', [IncomeController::class, 'edit'])->name('income.edit');
        Route::put('income/{id}', [IncomeController::class, 'update'])->name('income.update');
        Route::delete('income/{id}', [IncomeController::class, 'destroy'])->name('income.destroy');
        Route::get('new-expenses/create', [NewExpensesController::class, 'create'])->name('new-expenses.create');
        Route::post('new-expenses/store', [NewExpensesController::class, 'store'])->name('new-expenses.store');
        Route::get('new-expenses/reports', [NewExpensesController::class, 'reports'])->name('new-expenses.reports');
        Route::get('new-expenses/{id}/edit', [NewExpensesController::class, 'edit'])->name('new-expenses.edit');
        Route::put('new-expenses/{id}', [NewExpensesController::class, 'update'])->name('new-expenses.update');
        Route::delete('new-expenses/{id}', [NewExpensesController::class, 'destroy'])->name('new-expenses.destroy');
    });
    
    Route::get('billing/get-monthly-bills/{id}', [PatientController::class, 'getMonthlyBills']);
    Route::post('billing/update-monthly', [PatientController::class, 'updateMonthlyBill']);
    Route::post('billing/delete-monthly', [PatientController::class, 'deleteMonthlyBill']);
    Route::get('billing/get-payment/{ip_number}/{index}', [PatientController::class, 'getPayment']);
    Route::post('billing/update-payment', [PatientController::class, 'updatePayment']);
    Route::post('billing/delete-payment', [PatientController::class, 'deletePayment']);

    Route::get('patients/discharge/{id}', [DischargeController::class, 'discharge'])->name('patients.discharge.create');    

    Route::post('patient/discharge/confirm', [DischargeController::class, 'confirmDischarge'])->name('patient.discharge.confirm');
    Route::get('patient/discharge_summary/{id}', [DischargeController::class, 'showDischargeSummary'])
     ->name('patient.discharge.summary');
     
    Route::get('doctor-notes', [DoctornotesController::class, 'index'])->name('doctornotes.index');
    Route::get('doctor-notes/create', [DoctornotesController::class, 'create'])->name('doctornotes.create');
    Route::post('doctor-notes/store', [DoctornotesController::class, 'store'])->name('doctor.notes.store');
    Route::get('doctor-notes/edit/{id}', [DoctornotesController::class, 'edit'])->name('doctornotes.edit');
    Route::put('doctor-notes/{id}', [DoctornotesController::class, 'update'])->name('doctornotes.update');
    Route::delete('doctor-notes/{id}', [DoctornotesController::class, 'destroy'])->name('doctornotes.destroy');
    Route::get('doctor-notes-reports', [DoctornotesController::class, 'reports'])->name('doctornotes.reports');
    
    // Show discharge bill for selected patient
    Route::get('patient/discharge/{patient_id}', [DischargeController::class,'dischargeForm'])->name('patient.discharge.form');
    //Expenses
    Route::get('expenses/create', [ExpensesController::class, 'create'])->name('expenses.create');
    Route::get('expenses/index', [ExpensesController::class, 'index'])->name('expenses.index');
    Route::post('expenses/store', [ExpensesController::class, 'store'])->name('expenses.store');
    Route::get('expenses/edit/{id}', [ExpensesController::class, 'edit'])->name('expenses.edit');
    Route::post('expenses/update/{id}', [ExpensesController::class, 'update'])->name('expenses.update');
    Route::delete('expenses/{id}', [ExpensesController::class, 'destroy'])->name('expenses.destroy');
    Route::get('expenses/reports', [ExpensesController::class, 'reports'])->name('expenses.reports');
    Route::get('expenses/getexpensescategory/{id}', [ExpensesController::class, 'getExpensesByCategory'])->name('expenses.getByCategory');

    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('reports/create', [ReportsController::class, 'create'])->name('reports.create');
    Route::post('reports/store', [ReportsController::class, 'store'])->name('reports.store');
    Route::get('reports/edit/{id}', [ReportsController::class, 'edit'])->name('reports.edit');
    Route::put('reports/update/{id}', [ReportsController::class, 'update'])->name('reports.update');
    Route::delete('reports/delete/{id}', [ReportsController::class, 'destroy'])->name('reports.destroy');

    Route::get('dangerously-ill', [DangerouslyIllController::class, 'index']);
    Route::get('dangerously-ill/create', [DangerouslyIllController::class, 'create']);
    Route::post('dangerously-ill/store', [DangerouslyIllController::class, 'store']);
    Route::get('dangerously-ill/edit/{id}', [DangerouslyIllController::class, 'edit']);
    Route::post('dangerously-ill/update/{id}', [DangerouslyIllController::class, 'update']);
    Route::get('dangerously-ill/delete/{id}', [DangerouslyIllController::class, 'destroy']);
    Route::get('dangerously-ill/patient/{id}', [DangerouslyIllController::class, 'getPatient']);


    Route::get('injuries', [InjuryController::class, 'index'])->name('injuries.index');
    Route::get('injuries/create', [InjuryController::class, 'create'])->name('injuries.create');
    Route::post('injuries/store', [InjuryController::class, 'store'])->name('injuries.store');
    Route::get('injuries/edit/{id}', [InjuryController::class, 'edit'])->name('injuries.edit');
    Route::post('injuries/update/{id}', [InjuryController::class, 'update'])->name('injuries.update');
    Route::delete('injuries/delete/{id}', [InjuryController::class, 'destroy'])->name('injuries.destroy');
    Route::get('injuries/patient/{id}', [InjuryController::class, 'getPatientDetails']);

    Route::get('equipments', [MedicalEquipmentController::class, 'index'])->name('equipments.index');
    Route::get('equipments/create', [MedicalEquipmentController::class, 'create'])->name('equipments.create');
    Route::post('equipments/store', [MedicalEquipmentController::class, 'store'])->name('equipments.store');
    Route::get('equipments/edit/{id}', [MedicalEquipmentController::class, 'edit'])->name('equipments.edit');
    Route::post('equipments/update/{id}', [MedicalEquipmentController::class, 'update'])->name('equipments.update');
    Route::delete('equipments/delete/{id}', [MedicalEquipmentController::class, 'destroy'])->name('equipments.delete');

    Route::get('hearings', [PatientHearingController::class, 'index'])->name('hearings.index');
    Route::get('hearings/create', [PatientHearingController::class, 'create'])->name('hearings.create');
    Route::post('hearings/store', [PatientHearingController::class, 'store'])->name('hearings.store');
    Route::get('hearings/edit/{id}', [PatientHearingController::class, 'edit'])->name('hearings.edit');
    Route::post('hearings/update/{id}', [PatientHearingController::class, 'update'])->name('hearings.update');
    Route::delete('hearings/delete/{id}', [PatientHearingController::class, 'destroy'])->name('hearings.destroy');

// AJAX route to fetch admission date
Route::get('patients/get-admission-date/{id}', [PatientHearingController::class, 'getAdmissionDate']);

    Route::get('medicine-issues', [MedicineIssueController::class, 'index']);
    Route::get('medicine-issues/create', [MedicineIssueController::class, 'create']);
    Route::post('medicine-issues', [MedicineIssueController::class, 'store']);
    Route::get('medicine-issues/{id}/edit', [MedicineIssueController::class, 'edit']);
    Route::put('medicine-issues/{id}', [MedicineIssueController::class, 'update']);
    Route::delete('medicine-issues/{id}', [MedicineIssueController::class, 'destroy']);
    Route::get('get-patient/{id}', [MedicineIssueController::class, 'getPatient']);
    // Medicine Issue Reports
    Route::get('medicine-issues/reports', [MedicineIssueController::class, 'report'])->name('medicine-issues.report');
    Route::post('medicine-issues/reports', [MedicineIssueController::class, 'reportFilter'])->name('medicine-issues.report.filter');
    Route::get('medicine-issues/{bill_id}/view', [MedicineIssueController::class, 'show'])
    ->name('medicine-issues.view');
    Route::post('medicine-issues/add-payment', [MedicineIssueController::class, 'addNextPayment'])->name('medicine-issues.addPayment');
    Route::get('medicine-issues/payments/{bill_id}', [MedicineIssueController::class, 'getPayments']);

    //Stock
     Route::get('stock', [MedicineIssueController::class, 'stock']);


     //Loan
    Route::get('loan/index', [LoanController::class,'index'])->name('loan.index');
    Route::post('loan/store', [LoanController::class,'store'])->name('loan.store');
    Route::get('loan/edit/{id}', [LoanController::class,'edit'])->name('loan.edit');
    Route::post('loan/update/{id}', [LoanController::class,'update'])->name('loan.update');
    Route::delete('loan/destroy/{id}', [LoanController::class,'destroy'])->name('loan.destroy');


    // Death Register
    Route::get('death', [DeathRegisterController::class, 'index'])->name('death_register.index');
    Route::get('death/create', [DeathRegisterController::class, 'create']);
    Route::get('death/reports', [DeathRegisterController::class, 'reports'])->name('death.reports');
    Route::post('death/store', [DeathRegisterController::class, 'store'])->name('death_register.store');
    Route::get('death/edit/{id}', [DeathRegisterController::class, 'edit']);
    Route::put('death/update/{id}',[DeathRegisterController::class,'update']);
    Route::delete('death/delete/{id}', [DeathRegisterController::class, 'destroy'])->name('death.destroy');

    // Escape Register
    Route::get('escape_view', [EscapeRegisterController::class,'index'])->name('escape.index');
    Route::get('escape/reports', [EscapeRegisterController::class,'reports'])->name('escape.reports');
    Route::get('escape/create', [EscapeRegisterController::class,'create'])->name('escape.create');
    Route::post('escape/store', [EscapeRegisterController::class,'store'])->name('escape.store');
    Route::get('escape/edit/{id}', [EscapeRegisterController::class,'edit'])->name('escape.edit');
    Route::post('escape/update/{id}', [EscapeRegisterController::class,'update'])->name('escape.update');
    Route::delete('escape/delete/{id}', [EscapeRegisterController::class,'destroy'])->name('escape.destroy');
    
    Route::get('counsellor-notes', [CounsellornotesController::class, 'index'])->name('counsellornotes.index');
    Route::get('counsellornotes/create', [CounsellornotesController::class, 'create'])->name('counsellornotes.create');
    Route::post('counsellornotes', [CounsellornotesController::class, 'store'])->name('counsellornotes.store');
    Route::get('counsellornotes/{id}/edit', [CounsellornotesController::class, 'edit'])->name('counsellornotes.edit');
    Route::put('counsellornotes/{id}', [CounsellornotesController::class, 'update'])->name('counsellornotes.update');
    Route::delete('counsellornotes/{id}', [CounsellornotesController::class, 'destroy'])->name('counsellornotes.destroy');
    Route::get('counsellornotes/reports', [CounsellornotesController::class, 'reports'])->name('counsellornotes.reports'); 
    
    // Emergency    
    
    Route::get('emergency', [EmergencyController::class, 'index'])->name('emergency.index');
    Route::get('emergency/create', [EmergencyController::class, 'create'])->name('emergency.create');
    Route::post('emergency/store', [EmergencyController::class, 'store'])->name('emergency.store');
    Route::get('emergency/{id}/edit', [EmergencyController::class, 'edit'])->name('emergency.edit');
    Route::post('emergency/{id}/update', [EmergencyController::class, 'update'])->name('emergency.update');
    Route::delete('emergency/{id}', [EmergencyController::class, 'destroy'])->name('emergency.destroy');
    Route::get('emergency/{id}', [EmergencyController::class, 'show'])->name('emergency.show');

    // Patient Other Expenses
     Route::get('patient-expense', [PatientExpensesController::class, 'index']);
     Route::post('expense-store', [PatientExpensesController::class, 'store']);
    Route::post('expense-pay/{id}', [PatientExpensesController::class, 'pay']);
      Route::delete('expense-delete/{id}', [PatientExpensesController::class, 'destroy']);
    Route::get('patient-expense/payments/{id}', [PatientExpensesController::class, 'getPayments']);
    Route::get('patient-expense/edit/{id}', [PatientExpensesController::class, 'edit']);
    Route::post('patient-expense/update/{id}', [PatientExpensesController::class, 'update']);
    Route::get('patient-expense/delete/{id}', [PatientExpensesController::class, 'destroy']);
 

    //
    Route::prefix('emergency_call_register')->group(function () {
    Route::get('/', [EmergencyCallController::class, 'index'])->name('emergency.index');
    Route::get('/create', [EmergencyCallController::class, 'create'])->name('emergency.create');
    Route::post('/store', [EmergencyCallController::class, 'store'])->name('emergency.store');
    Route::get('/{id}/edit', [EmergencyCallController::class, 'edit'])->name('emergency.edit');
    Route::post('/{id}/update', [EmergencyCallController::class, 'update'])->name('emergency.update');
    Route::delete('/{id}', [EmergencyCallController::class, 'destroy'])->name('emergency.destroy');
});


});
