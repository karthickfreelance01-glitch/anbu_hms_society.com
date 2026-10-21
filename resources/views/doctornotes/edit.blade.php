@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">

            <h3 class="text-success fw-bold mb-4">Edit Doctor Note</h3>

            <div class="card shadow-lg border-0 rounded-4 p-4">
                <form action="{{ route('doctornotes.update', $note->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Select Patient <span class="text-danger">*</span></label>
                            <select name="patient_id" class="form-select select2" required>
                                <option value="">Select Patient</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ $note->patient_id == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="note_date" class="form-control" value="{{ $note->note_date }}" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Height</label>
                            <input type="text" name="height" class="form-control" value="{{ $note->height }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Weight</label>
                            <input type="text" name="weight" class="form-control" value="{{ $note->weight }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">BP</label>
                            <input type="text" name="bp" class="form-control" value="{{ $note->bp }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">PR</label>
                            <input type="text" name="pr" class="form-control" value="{{ $note->pr }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Temp</label>
                            <input type="text" name="temp" class="form-control" value="{{ $note->temp }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">SpO₂</label>
                            <input type="text" name="spo2" class="form-control" value="{{ $note->spo2 }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" rows="3" class="form-control">{{ $note->notes }}</textarea>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-gradient px-5 shadow">Update</button>
                        <a href="{{ route('doctornotes.index') }}" class="btn btn-secondary ms-2 px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Styling -->
<style>
.btn-gradient {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    color: #fff;
    border: none;
}
.btn-gradient:hover {
    transform: scale(1.03);
    box-shadow: 0 0 10px rgba(76,175,80,0.3);
}
.form-label {
    font-weight: 600;
    color: #333;
}
</style>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
$(document).ready(function() {
    $('.select2').select2({ width: '100%' });
});
</script>
@endsection
