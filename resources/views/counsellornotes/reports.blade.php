@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <!-- Header -->
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <h4 class="mb-0">Doctor Notes Reports</h4>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Doctor Notes</li>
                    </ol>
                </div>

                <!-- Filter Form -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('doctornotes.reports') }}" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Select Patient</label>
                                <select name="patient_id" class="form-select select2" id="patientSelect">
                                    <option value="">-- All Patients --</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">From Date</label>
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">To Date</label>
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>

                            <div class="col-md-2 text-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                            </div>

                            <div class="col-md-12 text-end">
                                <a href="{{ route('doctornotes.reports') }}" class="btn btn-secondary mt-2">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="card shadow-lg">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Doctor Notes List</h4>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="doctorNotesTable" class="table table-bordered table-striped text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Patient Name</th>
                                        <th>Date</th>
                                        <th>BP</th>
                                        <th>Temp</th>
                                        <th>SpO₂</th>
                                        <th>Notes</th>
                                        <th>Document</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($doctorNotes as $index => $note)
                                    <tr id="row-{{ $note->id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $note->patient_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($note->note_date)->format('d-m-Y') }}</td>
                                        <td>{{ $note->bp }}</td>
                                        <td>{{ $note->temp }}</td>
                                        <td>{{ $note->spo2 }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($note->notes, 30) }}</td>

                                        <!-- Document View -->
                                        <td>
                                            @if($note->document)
                                                @php
                                                    $ext = pathinfo($note->document, PATHINFO_EXTENSION);
                                                @endphp
                                                @if(in_array($ext, ['jpg','jpeg','png']))
                                                    <a href="{{ asset('storage/' . $note->document) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $note->document) }}" alt="doc" width="40" height="40" class="rounded border">
                                                    </a>
                                                @elseif($ext === 'pdf')
                                                    <a href="{{ asset('storage/' . $note->document) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                                        <i class="fa-solid fa-file-pdf"></i> View PDF
                                                    </a>
                                                @else
                                                    <a href="{{ asset('storage/' . $note->document) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fa-solid fa-file"></i> View File
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-muted">No File</span>
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-sm btn-info view-note-btn"
                                                data-note='@json($note)'>
                                                <i class="fa-solid fa-eye"></i> View
                                            </button>

                                            <a href="{{ route('doctornotes.edit', $note->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>

                                            <form action="{{ route('doctornotes.destroy', $note->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this note?')">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </form>
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

<!-- Modal -->
<div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="noteModalLabel">Doctor Note Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tr><th>Patient Name</th><td id="modal-patient-name"></td></tr>
          <tr><th>Date</th><td id="modal-date"></td></tr>
          <tr><th>BP</th><td id="modal-bp"></td></tr>
          <tr><th>Temp</th><td id="modal-temp"></td></tr>
          <tr><th>SpO₂</th><td id="modal-spo2"></td></tr>
          <tr><th>Notes</th><td id="modal-notes"></td></tr>
          <tr><th>Document</th><td id="modal-document"></td></tr>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap & DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- Buttons Extension -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({ placeholder: "Select a patient", allowClear: true });

    // DataTable
    $('#doctorNotesTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [[0, 'asc']],
        dom: "<'row mb-3'<'col-sm-6'f><'col-sm-6 text-end'B>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-danger' },
            { extend: 'csv', className: 'btn btn-sm btn-danger' },
            { extend: 'excel', className: 'btn btn-sm btn-danger' },
            { extend: 'pdf', className: 'btn btn-sm btn-danger' },
            { extend: 'print', className: 'btn btn-sm btn-danger' }
        ]
    });

    // Modal View
    $(document).on('click', '.view-note-btn', function() {
        var note = $(this).data('note');
        $('#modal-patient-name').text(note.patient_name);
        $('#modal-date').text(moment(note.note_date).format('DD-MM-YYYY'));
        $('#modal-bp').text(note.bp || '-');
        $('#modal-temp').text(note.temp || '-');
        $('#modal-spo2').text(note.spo2 || '-');
        $('#modal-notes').text(note.notes || '-');

        if (note.document) {
            let ext = note.document.split('.').pop().toLowerCase();
            let fileUrl = "{{ asset('storage') }}/" + note.document;
            if (['jpg','jpeg','png'].includes(ext)) {
                $('#modal-document').html(`<img src="${fileUrl}" class="img-fluid rounded border" width="150">`);
            } else if (ext === 'pdf') {
                $('#modal-document').html(`<a href="${fileUrl}" target="_blank" class="btn btn-outline-danger"><i class="fa-solid fa-file-pdf"></i> View PDF</a>`);
            } else {
                $('#modal-document').html(`<a href="${fileUrl}" target="_blank" class="btn btn-outline-secondary"><i class="fa-solid fa-file"></i> View File</a>`);
            }
        } else {
            $('#modal-document').html('<span class="text-muted">No File</span>');
        }

        $('#noteModal').modal('show');
    });
});
</script>

<!-- Moment.js for date formatting -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

@endsection
