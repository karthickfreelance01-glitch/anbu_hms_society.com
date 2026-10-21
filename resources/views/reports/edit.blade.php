@extends('layout.admin')
@section('content')

<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            <h3 class="text-success fw-bold mb-4">Edit Patient Reports</h3>

            <div class="card shadow p-4 border-0">
                <form action="{{ route('patientdocuments.update', $document->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Patient Name</label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">-- Select Patient --</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ $document->patient_id == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Date</label>
                            <input type="date" name="document_date" value="{{ $document->document_date }}" class="form-control" required>
                        </div>
                    </div>

                    <hr>

                    <h5 class="text-primary mb-3">Edit Uploaded Reports</h5>

                    <div id="document-rows">
                        @foreach($document->reports as $index => $report)
                            <div class="row align-items-center mb-3 document-row">
                                <div class="col-md-4">
                                    <label class="form-label">Document Name</label>
                                    <input type="text" name="report_name[]" value="{{ $report->report_name }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Replace File (optional)</label>
                                    <input type="file" name="report_file[]" class="form-control">
                                    <small class="text-muted">Current: <a href="{{ asset('uploads/reports/'.$report->file) }}" target="_blank">{{ $report->file }}</a></small>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" id="add-row" class="btn btn-secondary mb-3">+ Add More</button>

                    <div class="text-end">
                        <button type="submit" class="btn btn-gradient px-4">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.btn-gradient {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    color: #fff;
    border: none;
}
.btn-gradient:hover {
    opacity: 0.9;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('document-rows');
    const addRowBtn = document.getElementById('add-row');

    addRowBtn.addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.classList.add('row', 'align-items-center', 'mb-3', 'document-row');
        newRow.innerHTML = `
            <div class="col-md-4">
                <label class="form-label">Document Name</label>
                <input type="text" name="report_name[]" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Upload File</label>
                <input type="file" name="report_file[]" class="form-control" required>
            </div>
            <div class="col-md-2 mt-4">
                <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
            </div>
        `;
        container.appendChild(newRow);
    });

    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('.document-row').remove();
        }
    });
});
</script>

@endsection
