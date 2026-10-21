@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <!-- GLASS CARD -->
                <div class="card card-glass border-0 p-4 mt-4 animate__animated animate__fadeIn">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-gradient-animate">🏥 Patient Registration Form</h2>
                        <p class="text-muted">Please fill out the required details carefully</p>
                        <hr class="animated-hr mt-2 mb-3 mx-auto w-50">
                    </div>

                    <form id="patientForm" enctype="multipart/form-data" method="POST" action="{{ route('patients.store') }}">
                        @csrf

                        <!-- PHOTO UPLOAD -->
                        <div class="section-title mb-3">👤 Personal Details</div>
                        <div class="row justify-content-end">
                            <div class="col-md-4 text-center">
                                <div class="photo-box" id="previewBox">Click to Upload</div>
                                <input type="file" name="photo" id="photoInput" class="d-none" accept="image/*">

                                <button type="button" class="btn-upload-modern mt-2"
                                    onclick="document.getElementById('photoInput').click()">
                                    <i class="bi bi-cloud-upload"></i> Upload Photo
                                </button>
                            </div>
                        </div>

                        <!-- FORM FIELDS -->
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label class="form-label">IP Number <span class="text-danger">*</span></label>
                                <input type="text" name="ip_number" class="form-control" required autofocus>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                                <input type="date" id="date_of_admission" name="date_of_admission" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Admission Time <span class="text-danger">*</span></label>
                                <input type="text" id="time_of_admission" name="time_of_admission" class="form-control" placeholder="hh:mm AM/PM" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Patient Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Age <span class="text-danger">*</span></label>
                                <input type="number" name="age" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-4">
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
                            <div class="col-md-4">
                                <label class="form-label">Occupation</label>
                                <input type="text" name="occupation" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Guardian</label>
                                <input type="text" name="admitted_by" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                                <input type="tel" name="contact_number" class="form-control"
                                       placeholder="9999999999" maxlength="10" pattern="^\d{10}$"
                                       oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Referral Source</label>
                                <input type="text" name="referral_source" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Patient Case</label>
                                <input type="text" name="patient_case" class="form-control">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- ADDRESS SECTION -->
                        <div class="section-title mb-3">📍 Address & ID Details</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Present Address <span class="text-danger">*</span></label>
                                <textarea name="present_address" class="form-control" rows="2" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Permanent Address</label>
                                <textarea name="permanent_address" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Blood Group</label>
                                <input type="text" name="blood_group" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Staff Name</label>
                                <select class="form-control select2" name="staff_name">
                                    <option value="">Select Staff</option>
                                    @foreach($staff as $s)
                                        <option value="{{$s->staff_name}}">{{$s->staff_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- BILLING SECTION -->
                        <div class="section-title mb-3">💰 Billing Information</div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Monthly (₹)</label>
                                <input type="number" id="monthly_amount" name="monthly_amount" class="form-control" step="0.01" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Per Day (₹)</label>
                                <input type="number" id="per_day_amount" name="per_day_amount" class="form-control" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Total Amount (₹)</label>
                                <input type="number" id="total_amount" name="total_amount" class="form-control" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Received (₹)</label>
                                <input type="number" id="received_amount" name="received_amount" class="form-control" step="0.01" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Pending (₹)</label>
                                <input type="number" id="pending_amount" name="pending_amount" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Payment Mode</label>
                                <select name="payment_mode" class="form-select select2" required>
                                    <option value="">Select</option>
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="upi">UPI</option>
                                    <option value="net_banking">Net Banking</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Account Type</label>
                                <select name="accounttype" class="form-control select2" required>
                                    <option value="">Select</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Savings-SBI">Savings SBI</option>
                                    <option value="Savings_TMB">Savings TMB</option>
                                    <option value="Current account">Current account</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- SUBMIT -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-submit-modern">Submit Registration</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- LIBRARIES -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"/>
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
        $('#per_day_amount').val((monthly / 30).toFixed(2));

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
<script>
document.getElementById("photoInput").addEventListener("change", function(e){
    const file = e.target.files[0];
    if(!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById("previewBox").innerHTML =
            `<img src="${e.target.result}" style="width:100%;height:100%;border-radius:16px;">`;
    };
    reader.readAsDataURL(file);
});

$(function(){
    $('.select2').select2({ width:'100%' });

    $('#monthly_amount, #received_amount').on('input', function(){
        const monthly = parseFloat($('#monthly_amount').val()) || 0;
        const received = parseFloat($('#received_amount').val()) || 0;
        const perDay = (monthly / 30).toFixed(2);
        const pending = (monthly - received).toFixed(2);

        $('#per_day_amount').val(perDay);
        $('#total_amount').val(monthly.toFixed(2));
        $('#pending_amount').val(pending > 0 ? pending : 0);
    });
});
</script>

<style>
/* Upload Button */
.btn-upload-modern {
    background: linear-gradient(90deg, #7F00FF, #3f2b96, #00DBDE);
    border: none;
    padding: 10px 28px;
    border-radius: 50px;
    color: #fff;
    font-weight: 700;
    font-size: 14px;
    transition: 0.3s ease;
    box-shadow: 0 4px 15px rgba(127,0,255,0.3);
}
.btn-upload-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,219,222,0.4);
}

/* Submit Button */
.btn-submit-modern {
    background: linear-gradient(135deg, #2575fc, #2af598);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 12px 40px;
    font-weight: 700;
    box-shadow: 0 6px 18px rgba(42,245,152,0.3);
    transition: 0.3s;
}
.btn-submit-modern:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 22px rgba(42,245,152,0.4);
}

/* Card */
.card-glass {
    background: rgba(255,255,255,0.3);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.4);
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
}

/* Title */
.text-gradient-animate {
    background: linear-gradient(90deg, #6a11cb, #2575fc, #2af598, #009efd);
    background-size: 300% 100%;
    -webkit-background-clip: text;
    color: transparent;
    animation: gradientMove 4s linear infinite;
}
@keyframes gradientMove {
    0%{ background-position:0% 0%; }
    100%{ background-position:100% 0%; }
}

/* Underline */
hr.animated-hr {
    height: 3px;
    background: linear-gradient(to left, #6a11cb, #2575fc);
    border: none;
    animation: slideLeft 2s ease-out;
}
@keyframes slideLeft { from{width:0%;} to{width:50%;} }

/* Photo Box */
.photo-box {
    height: 160px;
    border-radius: 16px;
    background: #eaf6ff;
    box-shadow: inset 6px 6px 12px #c7dbe8, inset -6px -6px 12px #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2575fc;
    font-weight: 600;
    cursor: pointer;
}
</style>
@endsection
