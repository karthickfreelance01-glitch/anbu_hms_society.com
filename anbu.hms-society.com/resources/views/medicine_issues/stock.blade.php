@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="text-primary fw-bold">Medicine Stock</h3>
                </div>

                <div class="card shadow-lg rounded-4">
                    <div class="card-body">
                        <table class="table table-bordered" id="stockTable">
                            <thead class="table-secondary">
                                <tr class="text-center">
                                    <th class="text-center">#</th>
                                    <th class="text-center">Drug Name</th>
                                    <th class="text-center"> Stock</th>
                                    <th class="text-center">Issued</th>
                                    <th class="text-center">Current Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($medicines as $medicine)
                                <tr class="text-center align-middle">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $medicine->drug_name }}</td>
                                    <td>{{ $medicine->stock }}</td>
                                    <td>{{ $medicine->issued_count }}</td>
                                    <td>{{ $medicine->stock - $medicine->issued_count }}</td>
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
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    let table = $('#stockTable').DataTable({
        dom:
            "<'row mb-3'<'col-sm-6'f><'col-sm-6 text-end'B>>" + // search left, buttons right
            "rt" + // table
            "<'row mt-3'<'col-sm-12'p>>", // pagination
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});
</script>
@endsection
