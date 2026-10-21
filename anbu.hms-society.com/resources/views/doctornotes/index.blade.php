@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <!-- Header -->
                <div class="d-flex justify-content-between mb-3">
                    <h4>Doctor Notes Reports</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Doctor Notes</li>
                    </ol>
                </div>

                <!-- Table Card -->
                <div class="card shadow-lg">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Doctor Notes List</h4>
                        <a href="{{ route('doctornotes.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus-circle me-1"></i> Add
                        </a>
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
                                                    <a href="{{ asset($note->document) }}" target="_blank">
                                                        <img src="{{ asset($note->document) }}" alt="doc" width="40" height="40" class="rounded border">
                                                    </a>
                                                @elseif($ext === 'pdf')
                                                    <a href="{{ asset($note->document) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                                        <i class="fa-solid fa-file-pdf"></i> View PDF
                                                    </a>
                                                @else
                                                    <a href="{{ asset($note->document) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fa-solid fa-file"></i> View File
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-muted">No File</span>
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="d-flex justify-content-center gap-1">
                                            <!-- View Modal Trigger -->
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
<div class="modal fade" id="viewNoteModal" tabindex="-1" aria-labelledby="viewNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-gradient text-white">
                <h5 class="modal-title fw-bold" id="viewNoteModalLabel">Doctor Note Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div id="modalContent">
                    <!-- Dynamically filled -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<script>
$(document).ready(function() {
    $('#doctorNotesTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: [8] }],
        responsive: true
    });

    // View Modal
    $(document).on('click', '.view-note-btn', function() {
        const note = $(this).data('note');
        let docHtml = '';

        if (note.document) {
            const ext = note.document.split('.').pop().toLowerCase();
            const fileUrl = "{{ url('/') }}/" + note.document;

            if (['jpg','jpeg','png'].includes(ext)) {
                docHtml = `<img src="${fileUrl}" class="img-fluid rounded shadow-sm mt-2" alt="Document">`;
            } else if (ext === 'pdf') {
                docHtml = `<iframe src="${fileUrl}" width="100%" height="400" class="border rounded mt-2"></iframe>`;
            } else {
                docHtml = `<a href="${fileUrl}" target="_blank" class="btn btn-outline-secondary mt-2">
                              <i class="fa-solid fa-file"></i> View Document
                           </a>`;
            }
        } else {
            docHtml = `<p class="text-muted mt-2">No document attached.</p>`;
        }

        $('#modalContent').html(`
            <div class="row mb-3">
                <div class="col-md-6"><strong>Patient:</strong> ${note.patient_name}</div>
                <div class="col-md-6"><strong>Date:</strong> ${note.note_date}</div>
                <div class="col-md-4"><strong>Height:</strong> ${note.height ?? '-'}</div>
                <div class="col-md-4"><strong>Weight:</strong> ${note.weight ?? '-'}</div>
                <div class="col-md-4"><strong>BP:</strong> ${note.bp ?? '-'}</div>
                <div class="col-md-4"><strong>PR:</strong> ${note.pr ?? '-'}</div>
                <div class="col-md-4"><strong>Temp:</strong> ${note.temp ?? '-'}</div>
                <div class="col-md-4"><strong>SpO₂:</strong> ${note.spo2 ?? '-'}</div>
            </div>
            <hr>
            <h6 class="fw-bold text-primary">Notes:</h6>
            <p>${note.notes ?? '-'}</p>
            <h6 class="fw-bold text-primary mt-3">Attached Document:</h6>
            ${docHtml}
        `);

        $('#viewNoteModal').modal('show');
    });
});
</script>

<style>
.bg-gradient {
    background: linear-gradient(90deg, #4e73df, #1cc88a);
}
.btn-outline-primary.btn-sm {
    font-size: 0.85rem;
}
.modal-content {
    border-radius: 1rem;
    overflow: hidden;
}
</style>
@endsection
