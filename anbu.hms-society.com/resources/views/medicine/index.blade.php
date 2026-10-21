@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="text-primary fw-bold">Medicine List</h3>
                    <a href="{{ route('medicine.create') }}" class="btn btn-success">Add Medicine</a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-lg rounded-4">
                    <div class="card-body">
                         <div class="table-responsive">
                             <table class="table table-bordered" id="medicineTable">
                                <thead class="table-primary">
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Drug Name</th>
                                        <th>Category</th>
                                        <th>Vendor</th> <!-- Added -->
                                        <th>Mfg Date</th>
                                        <th>Expiry Date</th>
                                        <th>Price</th>
                                        <th>Selling Price</th>
                                        <th>Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($medicines as $medicine)
                                    <tr class="text-center align-middle">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $medicine->drug_name }}</td>
                                        <td>{{ $medicine->medicine_category }}</td>
                                        <td>{{ $medicine->vendor_name }}</td> <!-- Display vendor -->
                                        <td>{{ \Carbon\Carbon::parse($medicine->manufactured_date)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($medicine->expiry_date)->format('d-m-Y') }}</td>
                                        <td>₹{{ number_format($medicine->price, 2) }}</td>
                                        <td>₹{{ number_format($medicine->selling_price, 2) }}</td>
                                        <td>
                                            @if($medicine->stock > 10)
                                                <span class="badge bg-success">{{ $medicine->stock }}</span>
                                            @elseif($medicine->stock >=5)
                                                <span class="badge bg-warning text-dark">{{ $medicine->stock }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $medicine->stock }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center d-flex justify-content-center">
                                            <a href="{{ route('medicine.stock.edit', $medicine->id) }}" class="btn btn-sm btn-info me-2" title="Add Stock">
                                                Add Stock
                                            </a>
                                            <a href="{{ route('medicine.edit', $medicine->id) }}" class="btn btn-sm btn-primary me-2" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="deleteMedicine({{ $medicine->id }})" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
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

<!-- Hidden form for DELETE -->
<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#medicineTable').DataTable({
            "paging": false,
            "info": false,
            "searching": true,
        });
    });

    function deleteMedicine(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the medicine!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Set the action dynamically and submit the form
                let form = document.getElementById('deleteForm');
                form.action = 'medicine/delete/' + id;
                form.submit();
            }
        });
    }
</script>
@endsection
