@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Treatment & Therapy</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('treatment.update', $treatment->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Patient Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Patient ID</label>
                                <select name="patient_id" id="patient_id" class="form-control" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $p)
                                        <option value="{{ $p->patient_id }}" 
                                            data-name="{{ $p->full_name }}"
                                            {{ $treatment->patient_id == $p->patient_id ? 'selected' : '' }}>
                                            {{ $p->patient_id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Patient Name</label>
                                <input type="text" id="patient_name" class="form-control" 
                                       value="{{ $treatment->patient_name }}" readonly>
                            </div>
                        </div>

                        <!-- Medicine Selection (Multiple) -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label>Medicine & Dosage</label>
                                <select name="medicine_id[]" class="form-control" multiple required>
                                    @foreach($medicine as $m)
                                        <option value="{{ $m->id }}"
                                            {{ in_array($m->id, explode(',', $treatment->medicine_id)) ? 'selected' : '' }}>
                                            {{ $m->medicine_name }} - {{ $m->medicine_dosage }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple</small>
                            </div>
                        </div>

                        <!-- Prescribing Doctor -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Prescribing Doctor</label>
                                <select name="prescribing_doctor" class="form-control" required>
                                    <option value="">Select Doctor</option>
                                    @foreach($doctors as $d)
                                        <option value="{{ $d->id }}"
                                            {{ $treatment->prescribing_doctor == $d->id ? 'selected' : '' }}>
                                            {{ $d->staff_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Medicine Given Time -->
                            <div class="col-md-6">
                                <label>Medicine Given Time</label>
                                <input type="time" name="medicine_given_time" class="form-control" 
                                       value="{{ $treatment->medicine_given_time }}" required>
                            </div>
                        </div>

                        <!-- Therapy Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Therapy Name</label>
                                <select name="therapy_id" class="form-control">
                                    <option value="">Select Therapy</option>
                                    @foreach($therapy as $t)
                                        <option value="{{ $t->id }}"
                                            {{ $treatment->therapy_id == $t->id ? 'selected' : '' }}>
                                            {{ $t->therapy_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Therapy Doctor Name</label>
                                <input type="text" name="therapy_doctor" class="form-control" 
                                       value="{{ $treatment->therapy_doctor }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Therapy Timing</label>
                                <input type="time" name="therapy_time" class="form-control" 
                                       value="{{ $treatment->therapy_time }}">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('treatment.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Auto fetch patient name
    $('#patient_id').on('change', function() {
        let name = $(this).find(':selected').data('name');
        $('#patient_name').val(name || '');
    });
</script>
@endsection
