@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2 pb-3">
                <div class="flex-grow-1">
                    <h4 class="fw-bold mb-0">Expense Reports</h4>
                </div>
                <div class="text-end">
                    <ol class="breadcrumb m-0 py-0">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Reports</li>
                        <li class="breadcrumb-item active">Expense Report</li>
                    </ol>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="card mb-3">
                <div class="card-body">
                    <form id="filterForm">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <label for="from_date">From Date</label>
                                <input type="date" id="from_date" name="from_date" class="form-control" value="{{ $request->from_date }}">
                            </div>
                            <div class="col-md-2">
                                <label for="to_date">To Date</label>
                                <input type="date" id="to_date" name="to_date" class="form-control" value="{{ $request->to_date }}">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-secondary" onclick="resetFilter()" style="height: 32px;margin-top: 20px;">
                                    <i class="fa-solid fa-rotate-right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Expense Table -->
            <div class="card">
                <div class="card-header">
                    <h4>Expense List</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="expenseTable">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Name of the expense</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $index => $exp)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $exp->expense_name }}</td>
                                    <td>{{ $exp->expense_date }}</td>
                                    <td>₹ {{ number_format($exp->amount, 2) }}</td>
                                    <td>
                                        <a href="javascript:void(0)" class="editExpenseBtn" data-id="{{ $exp->id }}">
                                            <i class="fa-solid fa-pen-to-square text-success"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="deleteExpenseBtn" data-url="{{ url('expenses/delete/'.$exp->id) }}">
                                            <i class="fa-solid fa-trash text-danger"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">No expenses found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- SweetAlert2 & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){

    // Fetch expenses via AJAX
    function fetchExpenses() {
        var from_date = $('#from_date').val();
        var to_date   = $('#to_date').val();

        $.ajax({
            url: "{{ url('expenses/report') }}",
            type: 'GET',
            data: { from_date: from_date, to_date: to_date },
            dataType: 'json',
            success: function(response) {
                var rows = '';
                if (response.expenses.length > 0) {
                    $.each(response.expenses, function(index, exp){
                        rows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${exp.expense_name}</td>
                                <td>${exp.expense_date}</td>
                                <td>₹ ${parseFloat(exp.amount).toFixed(2)}</td>
                                <td>
                                    <a href="javascript:void(0)" class="editExpenseBtn" data-id="${exp.id}">
                                        <i class="fa-solid fa-pen-to-square text-success"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="deleteExpenseBtn" data-url="/expenses/delete/${exp.id}">
                                        <i class="fa-solid fa-trash text-danger"></i>
                                    </a>
                                </td>
                            </tr>`;
                    });
                } else {
                    rows = '<tr><td colspan="5" class="text-center">No expenses found.</td></tr>';
                }
                $('#expenseTable tbody').html(rows);
                bindDeleteButtons();
            }
        });
    }

    $('#from_date, #to_date').on('change', function(){
        fetchExpenses();
    });

    window.resetFilter = function(){
        $('#from_date, #to_date').val('');
        fetchExpenses();
    };

    function bindDeleteButtons(){
        $('.deleteExpenseBtn').off('click').on('click', function(e){
            e.preventDefault();
            var url = $(this).data('url');
            Swal.fire({
                title: "Are you sure?",
                text: "This expense will be deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if(result.isConfirmed){
                    window.location.href = url;
                }
            });
        });
    }

    bindDeleteButtons();
});
</script>
@endsection
