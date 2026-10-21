@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <div class="card shadow-lg border-0 rounded-4 p-4 mt-4 animate__animated animate__fadeIn">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-gradient">🏥 Patient Registration Form</h2>
                        <p class="text-muted">Please fill out the required details carefully</p>
                        <hr class="mt-3 mb-4 mx-auto w-50">
                    </div>

                    <form id="patientForm" enctype="multipart/form-data">
                        @csrf
                        <!-- Personal Details -->
                        <div class="section-title mb-3">👤 Personal Details</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Ip Number <span class="text-danger">*</span></label>
                                <input type="text" name="ip_number" class="form-control" value="{{ $newRegNo }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                                <input type="date" id="date_of_admission" name="date_of_admission" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Admission Time <span class="text-danger">*</span></label>
                                <input type="text" id="time_of_admission" name="time_of_admission" class="form-control" placeholder="hh:mm AM/PM" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Patient Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Full Name" required autofocus>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Age <span class="text-danger">*</span></label>
                                <input type="number" name="age" class="form-control" min="0" >
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select select2" required>
                                    <option value="">Select</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                    <option>Other</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Marital Status</label>
                                <select name="marital_status" class="form-select select2">
                                    <option value="">Select</option>
                                    <option>Single</option>
                                    <option>Married</option>
                                    <option>Widowed</option>
                                    <option>Divorced</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Occupation</label>
                                <input type="text" name="occupation" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Admitted By</label>
                                <input type="text" name="admitted_by" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                                <input type="tel" name="contact_number" class="form-control"
                                       placeholder="9999999999" maxlength="10" pattern="^\d{10}$"
                                       oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Referral Source</label>
                                <input type="text" name="referral_source" class="form-control">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Address & Aadhar -->
                        <div class="section-title mb-3">📍 Address & ID Details</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Present Address <span class="text-danger">*</span></label>
                                <textarea name="present_address" class="form-control" rows="2" ></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Permanent Address</label>
                                <textarea name="permanent_address" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Aadhar Number</label>
                                <input type="text" name="aadhar_number" class="form-control" placeholder="123456789012"
                                       pattern="\d{12}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Upload Photo</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Billing Section -->
                        <div class="section-title mb-3">💰 Billing Information</div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Monthly (₹)</label>
                                <input type="number" id="monthly_amount" name="monthly_amount" class="form-control" step="0.01"  required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Per Day (₹)</label>
                                <input type="number" id="per_day_amount" name="per_day_amount" class="form-control" step="0.01" min="0" value="0" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Pick Up (₹)</label>
                                <input type="number" id="pick_up" name="pick_up" class="form-control" step="0.01" min="0" value="0">
                            </div>
                            <!-- <div class="col-md-2">
                                <label class="form-label fw-bold">Medicine (₹)</label>
                                <input type="number" id="medicine" name="medicine" class="form-control" step="0.01" min="0" value="0">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Other Expense (₹)</label>
                                <input type="number" id="other_expense" name="other_expense" class="form-control" step="0.01" min="0" value="0">
                            </div> -->
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Total Amount (₹)</label>
                                <input type="number" id="total_amount" name="total_amount" class="form-control" readonly >
                            </div>
                            <div class="col-md-2 mt-3">
                                <label class="form-label fw-bold">Received (₹)</label>
                                <input type="number" id="received_amount" name="received_amount" class="form-control" step="0.01" min="0" value="0" required>
                            </div>
                            <div class="col-md-2 mt-3">
                                <label class="form-label fw-bold">Pending (₹)</label>
                                <input type="number" id="pending_amount" name="pending_amount" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Payment Mode</label>
                                <select name="payment_mode" id="payment_mode" class="form-select select2" required>
                                    <option value="">Select</option>
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="upi">UPI</option>
                                    <option value="net_banking">Net Banking</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-gradient btn-lg px-5 shadow-lg ">Submit </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<script>
$(document).ready(function () {
    $('.select2').select2({ width: '100%' });

    // Auto-fill date and time
    const now = new Date();
    $('#date_of_admission').val(now.toISOString().slice(0,10));
    let hours = now.getHours();
    const minutes = String(now.getMinutes()).padStart(2,'0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12;
    $('#time_of_admission').val(`${hours}:${minutes} ${ampm}`);
    $('input[name="name"]').focus();

    // --- Billing Calculations ---
    function getDaysInMonth(date) {
        const d = new Date(date);
        return new Date(d.getFullYear(), d.getMonth()+1, 0).getDate();
    }

    function updateBilling(){
        const daysInMonth = getDaysInMonth($('#date_of_admission').val()) || 30;

        let monthly = parseFloat($('#monthly_amount').val()) || 0;
        let perDay = parseFloat($('#per_day_amount').val()) || 0;

        // Update per day if monthly changed
        $('#per_day_amount').val((monthly / daysInMonth).toFixed(2));

        let pickUp = parseFloat($('#pick_up').val()) || 0;
        let medicine = parseFloat($('#medicine').val()) || 0;
        let other = parseFloat($('#other_expense').val()) || 0;

        let total = monthly + pickUp + medicine + other;
        $('#total_amount').val(total.toFixed(2));

        let received = parseFloat($('#received_amount').val()) || 0;
        let pending = total - received;
        $('#pending_amount').val(pending >= 0 ? pending.toFixed(2) : 0);
    }

    function updateMonthlyFromPerDay(){
        const daysInMonth = getDaysInMonth($('#date_of_admission').val()) || 30;
        const perDay = parseFloat($('#per_day_amount').val()) || 0;
        $('#monthly_amount').val((perDay * daysInMonth).toFixed(2));
        updateBilling();
    }

    $('#monthly_amount, #pick_up, #medicine, #other_expense, #received_amount').on('input', updateBilling);
    $('#per_day_amount').on('input', updateMonthlyFromPerDay);

    updateBilling();

    // AJAX submit
    let isSubmitting = false;
    $('#patientForm').submit(function(e){
        e.preventDefault();
        if(isSubmitting) return;
        isSubmitting = true;
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Registering...');

        $.ajax({
            url: "{{ route('patients.store') }}",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success:function(res){
                Swal.fire({ icon: 'success', title: 'Registered Successfully!', text: res.message })
                .then(() => window.location.href = "{{ route('patients.index') }}" );
            },
            error:function(xhr){
                isSubmitting = false;
                submitBtn.prop('disabled', false).html('Register Patient');
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let errMsg = '';
                    for(let key in errors){ errMsg += errors[key][0] + '<br>'; }
                    Swal.fire({ icon: 'error', title: 'Error!', html: errMsg });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error!', text: xhr.responseText });
                }
            }
        });
    });
});
</script>

<style>
.text-gradient {
    background: linear-gradient(45deg, #4e73df, #1cc88a);
    -webkit-background-clip: text;
    color: transparent;
}
.btn-gradient {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    color: white;
    border: none;
    transition: 0.3s;
}
.btn-gradient:hover {
    transform: scale(1.03);
    box-shadow: 0 0 12px rgba(76,175,80,0.3);
}
.section-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #4e73df;
    border-left: 4px solid #1cc88a;
    padding-left: 10px;
}
.form-label {
    font-weight: 600;
    color: #333;
}
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}
</style>
@endsection
