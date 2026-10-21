@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">
                <div class="card shadow-lg rounded-4 p-4 mt-4">
                    <h3 class="card-title text-center mb-4 text-primary fw-bold">Edit Expense</h3>

                    <form id="expenseEditForm" action="{{ route('expenses.update', $expense->id) }}" method="POST">
                        @csrf

                        <div class="row g-3">

                            <!-- Expense Category -->
                            <div class="col-md-6">
                                <label class="form-label">Expense Category</label>
                                <select name="expenses_id" id="expense_category" class="form-select select2" required>
                                    <option value="">Select Expense Category</option>
                                    @foreach($expensecat as $cat)
                                        <option value="{{ $cat->id }}" {{ $expense->expenses_id == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->expense_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Amount -->
                            <div class="col-md-3">
                                <label class="form-label">Amount (₹)</label>
                                <input type="number" step="0.01" min="0" name="amount" class="form-control"
                                       value="{{ $expense->amount }}" required>
                            </div>

                            <!-- Date -->
                            <div class="col-md-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="expense_date" class="form-control"
                                       value="{{ $expense->expense_date }}" required>
                            </div>

                            <!-- Notes -->
                            <div class="col-md-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" rows="3" class="form-control">{{ $expense->notes }}</textarea>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                                <span id="btnText">Update Expense</span>
                                <span id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary btn-lg px-5 ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS & Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function(){
    $('.select2').select2({ placeholder: "Choose a category", allowClear: true, width: '100%' });

    // Form submit with loading
    $('#expenseEditForm').submit(function(e){
        e.preventDefault();
        let btn = $('#submitBtn');
        let btnText = $('#btnText');
        let btnLoader = $('#btnLoader');

        btn.prop('disabled', true);
        btnText.text('Updating...');
        btnLoader.removeClass('d-none');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(res){
                Swal.fire({ icon: 'success', title: 'Updated', text: res.message })
                     .then(()=> window.location.href = "{{ route('expenses.index') }}");
            },
            error: function(xhr){
                btn.prop('disabled', false);
                btnText.text('Update Expense');
                btnLoader.addClass('d-none');

                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let errMsg = '';
                    for(let key in errors){ errMsg += errors[key][0] + '<br>'; }
                    Swal.fire({ icon: 'error', title: 'Validation Error', html: errMsg });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseText });
                }
            }
        });
    });
});
</script>
@endsection
