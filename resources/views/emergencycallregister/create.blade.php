@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')
    <div class="page-wrapper">
        <div class="content">
            <div class="card shadow-lg rounded-4 p-4 mt-4">
                <h4 class="text-center mb-4 fw-bold text-primary">Add Emergency Hospitalization</h4>

                <form id="emergencyForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Patient</label>
                            <select name="patient_id" class="form-select select2" required>
                                <option value="">Select Patient</option>
                                @foreach($patients as $p)
                                    <option value="{{ $p->id }}">{{ $p->reg_no }} - {{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Illness</label>
                            <input type="text" name="illness" class="form-control" placeholder="Enter illness">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Doctor</label>
                            <input type="text" name="doctor" class="form-control" placeholder="Enter doctor">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">💾 Save</button>
                        <a href="{{ route('emergency.index') }}" class="btn btn-secondary">↩ Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({ width:'100%' });

    $('#emergencyForm').submit(function(e){
        e.preventDefault();
        let formData = $(this).serialize();
        $.post("{{ route('emergency.store') }}", formData, function(res){
            Swal.fire('Success', res.message, 'success').then(()=> window.location.href="{{ route('emergency.index') }}");
        }).fail(function(xhr){
            let errors = xhr.responseJSON.errors;
            let errMsg = '';
            $.each(errors, function(key, value){ errMsg += value[0] + '<br>'; });
            Swal.fire('Error', errMsg, 'error');
        });
    });
});
</script>
@endsection
