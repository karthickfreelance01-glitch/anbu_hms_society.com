@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')
    <div class="page-wrapper">
        <div class="content">
            <div class="card shadow-lg rounded-4 p-4 mt-4">
                <h4 class="fw-bold mb-4 text-primary">Edit Emergency Record</h4>

                <form id="editEmergencyForm">
                    @csrf
                    <input type="hidden" name="id" value="{{ $record->id }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Patient</label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">Select Patient</option>
                                @foreach($patients as $p)
                                    <option value="{{ $p->id }}" {{ $record->patient_id == $p->id ? 'selected':'' }}>
                                        {{ $p->reg_no }} - {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" value="{{ $record->date }}" required>
                        </div>

                        <div class="col-md-6">
                            <label>Illness</label>
                            <input type="text" name="illness" class="form-control" value="{{ $record->illness }}">
                        </div>

                        <div class="col-md-6">
                            <label>Doctor</label>
                            <input type="text" name="doctor" class="form-control" value="{{ $record->doctor }}">
                        </div>

                        <div class="col-md-12">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control">{{ $record->remarks }}</textarea>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">💾 Update</button>
                        <a href="{{ route('emergency.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$('#editEmergencyForm').submit(function(e){
    e.preventDefault();
    let id = $('input[name=id]').val();
    let formData = $(this).serialize();

    $.ajax({
        url: "{{ url('emergency_call_register') }}/" + id + "/update",
        type: "POST",
        data: formData,
        success: function(res){
            Swal.fire('Updated', res.message, 'success')
                 .then(()=> window.location.href="{{ route('emergency.index') }}");
        },
        error: function(xhr){
            let errors = xhr.responseJSON.errors;
            let errMsg = '';
            $.each(errors, function(k,v){ errMsg += v[0] + '<br>'; });
            Swal.fire('Error', errMsg, 'error');
        }
    });
});

</script>
@endsection
