@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="card">
                <div class="card-header">
                    <h4>Add Treatment & Therapy</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('treatment.store') }}" method="POST">
                        @csrf

                        <!-- Patient Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Patient</label>
                                <select name="patient_id" id="patient_id" class="form-control" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $p)
                                        <option value="{{ $p->patient_id }}" data-name="{{ $p->full_name }}">
                                            {{ $p->patient_id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Patient Name</label>
                                <input type="text" id="patient_name" class="form-control" readonly>
                            </div>
                        </div>

                        <!-- Medicine & Doctor -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                 <label for="medicine_id">Medicine & Dosage</label>
                                    <select name="medicine_id[]" id="medicine_id" class="form-control select2" multiple="multiple" required>
                                        @foreach($medicine as $m)
                                            <option value="{{ $m->id }}">
                                                {{ $m->medicine_name }} - {{ $m->medicine_dosage }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">You can select multiple medicines</small>

                            </div>
                            <div class="col-md-6">
                                <label>Prescribing Doctor</label>
                                <select name="prescribing_doctor" class="form-control" required>
                                    <option value="">Select Doctor</option>
                                    @foreach($doctors as $d)
                                        <option value="{{ $d->id }}"
                                            {{ isset($treatment) && $treatment->prescribing_doctor == $d->id ? 'selected' : '' }}>
                                            {{ $d->staff_name }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <!-- Medicine & Therapy Timing -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Medicine Given Time</label>
                                <input type="time" name="medicine_given_time" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label>Therapy</label>
                                <select name="therapy_id" class="form-control">
                                    <option value="">Select Therapy</option>
                                    @foreach($therapy as $t)
                                        <option value="{{ $t->id }}">{{ $t->therapy_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Therapy Doctor</label>
                                <input type="text" name="therapy_doctor" class="form-control">
                            </div>
                            <div class="col-md-6">
                               <label for="therapy_time">Therapy Time</label>
                              <input type="time" id="therapy_time" name="therapy_time" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('treatment.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#patient_id').on('change', function() {
        let name = $(this).find(':selected').data('name');
        $('#patient_name').val(name || '');
    });
</script>

<script>
    $(document).ready(function() {
        $('#medicine_id').select2({
            placeholder: "Select medicines",
            allowClear: true,
            width: '100%'
        });
    });
</script>

@endsection
