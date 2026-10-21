@extends('layout.admin')
@section('content')

<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            <h3 class="text-success fw-bold mb-4">Edit FIR / CSR & Hearing Details</h3>

            <div class="card shadow p-4 border-0">
                <form action="{{ url('hearings/update/' . $hearing->id) }}" method="POST">
                    @csrf
                    

                    <div class="row mb-3">
                        <!-- Patient Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Patient Name</label>
                            <select class="form-select select2" id="patient_id" name="patient_id" required>
                                <option value="">Select Patient</option>
                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->id }}"
                                        {{ $hearing->patient_id == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date of Admission (Auto Fetched) -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date of Admission</label>
                            <input type="date" class="form-control" id="date_of_admission" name="date_of_admission"
                                value="{{  $hearing->date_of_admission }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- FIR/CSR -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">FIR / CSR</label>
                            <input type="text" class="form-control" name="fir_csr" value="{{  $hearing->fir_csr }}"
                                placeholder="Enter FIR or CSR number" required>
                        </div>

                        <!-- Date of Hearing -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date of Hearing</label>
                            <input type="date" class="form-control" name="date_of_hearing"
                                value="{{  $hearing->date_of_hearing }}" required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success px-4">Update</button>
                        <a href="{{ url('fir-hearing') }}" class="btn btn-secondary px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Auto-fetch Date of Admission -->
<script>
    $(document).ready(function() {
        $('#patient_id').on('change', function() {
            var patientId = $(this).val();
            if (patientId) {
                $.ajax({
                    url: '{{ url("get-admission-date") }}/' + patientId,
                    type: 'GET',
                    success: function(response) {
                        $('#date_of_admission').val(response.date_of_admission);
                    }
                });
            } else {
                $('#date_of_admission').val('');
            }
        });
    });
</script>

@endsection
