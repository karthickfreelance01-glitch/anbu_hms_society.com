@extends('layout.admin')
@section('content')

<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="card shadow border-0 rounded-4 p-4 mt-4">
                <h3 class="text-success fw-bold mb-4 text-center">➕ Add Dangerously Illness Record</h3>

                <form method="POST" action="{{ url('dangerously-ill/store') }}">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Select Patient <span class="text-danger">*</span></label>
                            <select name="patient_id" id="patient_id" class="form-select select2" required>
                                <option value="">Select Patient</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->ip_number }} - {{ $patient->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Admission Date</label>
                            <input type="text" id="admission_date" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Before Admission Condition</label>
                        <textarea name="before_admission_condition" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Treatment Given</label>
                        <textarea name="treatment_given" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-5">Save</button>
                        <a href="{{ url('dangerously-ill') }}" class="btn btn-secondary px-4 ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
$(document).ready(function(){
    $('.select2').select2();

   $('#patient_id').change(function(){
    var id = $(this).val();
    if(id){
        $.get("{{ url('dangerously-ill/patient') }}/" + id, function(data){
            $('#patient_name').val(data.name);
            $('#age').val(data.age);
            $('#admission_date').val(data.date_of_admission);
        }).fail(function(){
            Swal.fire('Error','Patient data not found!','error');
        });
    } else {
        $('#patient_name, #age, #admission_date').val('');
    }
});

});
</script>
@endsection
