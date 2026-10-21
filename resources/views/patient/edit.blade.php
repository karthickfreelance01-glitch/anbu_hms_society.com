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
                        <h2 class="fw-bold text-gradient">✏️ Edit Patient Details</h2>
                        <p class="text-muted">Update the patient's information carefully</p>
                        <hr class="mt-3 mb-4 mx-auto w-50">
                    </div>

                    <form id="editPatientForm" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <!-- Personal Details -->
                        <div class="section-title mb-3">👤 Personal Details</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Ip Number <span class="text-danger">*</span></label>
                                <input type="text" name="ip_number" class="form-control" value="{{ $patient->ip_number }}" >
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_admission" class="form-control" value="{{ $patient->date_of_admission }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Admission Time <span class="text-danger">*</span></label>
                                <input type="text" name="time_of_admission" class="form-control" value="{{ $patient->time_of_admission }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Patient Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ $patient->name }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Age <span class="text-danger">*</span></label>
                                <input type="number" name="age" class="form-control" min="0" value="{{ $patient->age }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select select2" required>
                                    <option value="">Select</option>
                                    <option value="Male" {{ $patient->gender=='Male'?'selected':'' }}>Male</option>
                                    <option value="Female" {{ $patient->gender=='Female'?'selected':'' }}>Female</option>
                                    <option value="Other" {{ $patient->gender=='Other'?'selected':'' }}>Other</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Marital Status</label>
                                <select name="marital_status" class="form-select select2">
                                    <option value="">Select</option>
                                    <option {{ $patient->marital_status=='Single'?'selected':'' }}>Single</option>
                                    <option {{ $patient->marital_status=='Married'?'selected':'' }}>Married</option>
                                    <option {{ $patient->marital_status=='Widowed'?'selected':'' }}>Widowed</option>
                                    <option {{ $patient->marital_status=='Divorced'?'selected':'' }}>Divorced</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Occupation</label>
                                <input type="text" name="occupation" class="form-control" value="{{ $patient->occupation }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Admitted By</label>
                                <input type="text" name="admitted_by" class="form-control" value="{{ $patient->admitted_by }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                                <input type="tel" name="contact_number" class="form-control" maxlength="10"
                                       value="{{ $patient->contact_number }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Referral Source</label>
                                <input type="text" name="referral_source" class="form-control" value="{{ $patient->referral_source }}">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Address & Aadhar -->
                        <div class="section-title mb-3">📍 Address & ID Details</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Present Address <span class="text-danger">*</span></label>
                                <textarea name="present_address" class="form-control" rows="2">{{ $patient->present_address }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Permanent Address</label>
                                <textarea name="permanent_address" class="form-control" rows="2">{{ $patient->permanent_address }}</textarea>
                            </div>
                           
                            <div class="col-md-6">
                                <label class="form-label">Upload New Photo</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                                @if($patient->photo)
                                    <img src="{{ asset('uploads/patients/'.$patient->photo) }}" class="rounded mt-2" style="width:100px;">
                                @endif
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Billing Section -->
                        <div class="section-title mb-3">💰 Billing Information</div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Monthly (₹)</label>
                                <input type="number" id="monthly_amount" name="monthly_amount" class="form-control" step="0.01"
                                       value="{{ $bill->monthly_amount ?? 0 }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Per Day (₹)</label>
                                <input type="number" id="per_day_amount" name="per_day_amount" class="form-control"
                                       value="{{ $bill->per_day_amount ?? 0 }}" readonly>
                            </div>
                           
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Total Amount (₹)</label>
                                <input type="number" id="total_amount" name="total_amount" class="form-control"
                                       value="{{ $bill->total_amount ?? 0 }}" readonly>
                            </div>
                            <div class="col-md-2 mt-3">
                                <label class="form-label fw-bold">Received (₹)</label>
                                <input type="number" id="received_amount" name="received_amount" class="form-control"
                                       value="{{ $bill->received_amount ?? 0 }}">
                            </div>
                            <div class="col-md-2 mt-3">
                                <label class="form-label fw-bold">Pending (₹)</label>
                                <input type="number" id="pending_amount" name="pending_amount" class="form-control"
                                       value="{{ $bill->pending_amount ?? 0 }}" readonly>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Payment Mode</label>
                                <select name="payment_mode" id="payment_mode" class="form-select select2" required>
                                    <option value="">Select</option>
                                    <option value="cash" {{ $bill->payment_mode=='cash'?'selected':'' }}>Cash</option>
                                    <option value="card" {{ $bill->payment_mode=='card'?'selected':'' }}>Card</option>
                                    <option value="upi" {{ $bill->payment_mode=='upi'?'selected':'' }}>UPI</option>
                                    <option value="net_banking" {{ $bill->payment_mode=='net_banking'?'selected':'' }}>Net Banking</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Account type</label>
                                <select name="accounttype" id="" class="form-control" required>
                                    <option value="">-- Select Account type --</option>
                                    <option value="Cash" {{ $bill->account_type=='Cash'?'selected':'' }}>Cash</option>
                                    <option value="Savings "{{ $bill->account_type=='Savings'?'selected':'' }}>Savings</option>
                                    <option value="Current account" {{ $bill->account_type=='Current account'?'selected':'' }}>Current account</option>
                                </select>
                            </div>
                        </div>
                        

                        <div class="text-center">
                            <button type="submit" class="btn btn-gradient btn-lg px-5 shadow-lg">Update</button>
                            <a href="{{ route('patients.index') }}" class="btn btn-secondary btn-lg px-4 ms-2">Cancel</a>
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

    function getDaysInMonth(date) {
        const d = new Date(date);
        return new Date(d.getFullYear(), d.getMonth()+1, 0).getDate();
    }

    function updateBilling(){
        const daysInMonth = getDaysInMonth($('input[name="date_of_admission"]').val()) || 30;
        let monthly = parseFloat($('#monthly_amount').val()) || 0;
        $('#per_day_amount').val((monthly / 30).toFixed(2));

        let pickUp = parseFloat($('#pick_up').val()) || 0;
        let total = monthly + pickUp;
        $('#total_amount').val(total.toFixed(2));

        let received = parseFloat($('#received_amount').val()) || 0;
        let pending = total - received;
        $('#pending_amount').val(pending >= 0 ? pending.toFixed(2) : 0);
    }

    $('#monthly_amount, #pick_up, #received_amount').on('input', updateBilling);
    updateBilling();

    // AJAX Update
    $('#editPatientForm').submit(function(e){
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: '{{ url("patients/$patient->id/update") }}',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success:function(res){
                Swal.fire('Success','Patient details updated successfully!','success')
                .then(()=> window.location.href='{{ route("patients.index") }}');
            },
            error:function(xhr){
                Swal.fire('Error',xhr.responseJSON?.message || 'Something went wrong','error');
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
</style>
@endsection
