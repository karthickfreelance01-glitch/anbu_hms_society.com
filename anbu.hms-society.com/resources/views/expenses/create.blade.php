@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">
                <div class="card shadow-lg rounded-4 p-4 mt-4">
                    <h3 class="card-title text-center mb-4 text-primary fw-bold">Add Expense</h3>

                    <form id="expenseForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">

                            <!-- Expense Category -->
                            <div class="col-md-6">
                                <label class="form-label">Expense Main Category</label>
                                <select name="expense_id" id="expense_category" class="form-select select2" required>
                                    <option value="">Select Category</option>
                                    @foreach($expenses as $cat)
                                        <option value="{{ $cat->id }}" >
                                            {{ $cat->expense_name }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>

                            <!-- Expenses (dependent) -->
                            

                            <!-- Amount -->
                            <div class="col-md-3">
                                <label class="form-label">Amount (₹)</label>
                                <input type="number" step="0.01" min="0" name="amount" class="form-control" required>
                            </div>

                            <!-- Date -->
                            <div class="col-md-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="expense_date" class="form-control" required>
                            </div>
                            <!-- Accounttype -->
                            <div class="col-md-4">
                                <label class="form-label">Account type</label>
                                <select name="accounttype" id="" class="form-control" required>
                                    <option value="">-- Select Account type --</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Savings-SBI">Savings SBI</option>
                                    <option value="Savings_TMB">Savings TMB</option>
                                    <option value="Current account">Current account</option>
                                     <option value="Other">Other</option>
                                </select>
                            </div>
                            <!-- Notes -->
                            <div class="col-md-8">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" rows="3" class="form-control"></textarea>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                                <span id="btnText">Save Expense</span>
                                <span id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                            <a href="{{ route('expenses.create') }}" class="btn btn-secondary btn-lg px-5 ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){

    // Initialize Select2
    $('.select2').select2({ placeholder: "Choose an option", allowClear: true, width: '100%' });

    
    // Submit form with loading button
    $('#expenseForm').submit(function(e){
        e.preventDefault();

        let btn = $('#submitBtn');
        let btnText = $('#btnText');
        let btnLoader = $('#btnLoader');

        // Prevent multiple submissions
        if(btn.prop('disabled')) return;

        btn.prop('disabled', true);
        btnText.text('Saving...');
        btnLoader.removeClass('d-none');

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('expenses.store') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                Swal.fire({ icon: 'success', title: 'Success', text: res.message })
                     .then(() => window.location.href = "{{ route('expenses.index') }}");
            },
            error: function(xhr){
                btn.prop('disabled', false);
                btnText.text('Save Expense');
                btnLoader.addClass('d-none');

                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let errMsg = '';
                    for(let key in errors){ errMsg += errors[key][0] + '<br>'; }
                    Swal.fire({ icon: 'error', title: 'Validation Error', html: errMsg });
                } else {
                    let msg = xhr.responseJSON?.message || xhr.responseText || 'Something went wrong!';
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                }
            }
        });
    });
});
</script>
@endsection
