@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="fw-bold text-primary">Expense Reports</h3>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Expense</a></li>
                        <li class="breadcrumb-item active"> Add Expense</li>
                    </ol>
                </div>

                <!-- Expense Table -->
                <div class="card shadow-lg rounded-4">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0">
                        <h5 class="mb-0">Expense List</h5>
                        <a href="{{ route('expenses.create') }}" class="btn btn-success shadow">
                            <i class="fa-solid fa-plus me-1"></i> Add Expense
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="expenseTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        
                                        <th>Expense Name</th>
                                        <th>Notes</th>
                                        <th>Amount (₹)</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenses as $key => $exp)
                                    <tr id="row-{{ $exp->id }}">
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $exp->expense_name }}</td>
                                        <td>{{ $exp->notes ?? '-' }}</td>
                                        <td>₹ {{ number_format($exp->amount, 2) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($exp->expense_date)->format('d-m-Y') }}</td>
                                        <td>
                                            <a href="{{ route('expenses.edit', $exp->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger delete-expense" data-id="{{ $exp->id }}" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- JS & DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){
    $('#expenseTable').DataTable({
        dom: "<'row mb-3'<'col-sm-6'f><'col-sm-6 text-end'B>>" +
             "<'row'<'col-12'tr>>" +
             "<'row mt-3'<'col-sm-6'i><'col-sm-6'p>>",
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });

    // Delete functionality
    $(document).on('click', '.delete-expense', function(){
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete this expense!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: '{{ url("expenses") }}/' + id,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res){
                        Swal.fire('Deleted!', res.message, 'success');
                        // Remove row from table without reload
                        $('#row-' + id).fadeOut(800, function(){ $(this).remove(); });
                    },
                    error: function(xhr){
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    });
});
</script>

<style>
.table th { background: #f8f9fa; font-weight: 600; }
.table td { vertical-align: middle; }
</style>
@endsection
