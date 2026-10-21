@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="d-flex justify-content-between mb-3">
                <h4>Treatment & Therapy Report</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Treatment Report</li>
                </ol>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form id="filterForm" class="row g-2">
                        <div class="col-md-3">
                            <label for="from_date">From Date</label>
                            <input type="date" id="from_date" name="from_date" class="form-control" value="{{ $request->from_date ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label for="to_date">To Date</label>
                            <input type="date" id="to_date" name="to_date" class="form-control" value="{{ $request->to_date ?? '' }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-secondary w-100" onclick="resetFilter()">Reset</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold">Day Report</h4>
                    <a href="{{ url('treatments/create') }}" class="btn btn-primary btn-sm">New Record</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Medicine</th>
                                    <th>Prescribing Doctor</th>
                                    <th>Medicine Time</th>
                                    <th>Therapy</th>
                                    <th>Therapy Doctor</th>
                                    <th>Therapy Time</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="treatmentTableBody">
                                @foreach($treatments as $key => $t)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $t->patient_id }}</td>
                                        <td>{{ $t->patient_name }}</td>
                                        <td>{{ $t->medicines }}</td>
                                        <td>{{ $t->doctor_name }}</td>
                                        <td>{{ $t->medicine_given_time }}</td>
                                        <td>{{ $t->therapy_name }}</td>
                                        <td>{{ $t->therapy_doctor }}</td>
                                        <td>{{ $t->therapy_time }}</td>
                                        <td class="d-flex">
                                            <a href="{{ route('treatment.edit', $t->id) }}" class="btn btn-warning btn-sm me-1">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm deleteTreatmentBtn" data-id="{{ $t->id }}">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function(){

    function fetchTreatments() {
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();

        $.ajax({
            url: "{{ route('treatment.index') }}",
            type: "GET",
            data: { from_date: from_date, to_date: to_date },
            dataType: "json",
            success: function(response){
                $('#treatmentTableBody').html(response.html);
                bindDeleteButtons();
            }
        });
    }

    $('#from_date, #to_date').on('change', function(){
        fetchTreatments();
    });

    window.resetFilter = function(){
        $('#from_date, #to_date').val('');
        fetchTreatments();
    }

    function bindDeleteButtons(){
        $('.deleteTreatmentBtn').off('click').on('click', function(){
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if(result.isConfirmed){
                    $.ajax({
                        url: "/treatment/" + id,
                        type: "DELETE",
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(){
                            Swal.fire('Deleted!', 'Record has been deleted.', 'success');
                            fetchTreatments();
                        }
                    });
                }
            });
        });
    }

    bindDeleteButtons();
});
</script>
@endsection
