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
                    <h4>Counsellor Notes Reports</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Counsellor Notes</li>
                    </ol>
                </div>

                <!-- Table Card -->
                <div class="card shadow-lg">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Counsellor Notes List</h4>
                        <a href="{{ route('counsellornotes.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus-circle me-1"></i> Add New
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="counsellorNotesTable" class="table table-bordered table-striped text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Patient Name</th>
                                        <th>Date</th>
                                        <th>Counsellor</th>
                                        <th>Assessed Problem</th>
                                        <th>Counselling Given</th>
                                        <th>Remarks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($doctorNotes as $index => $note)
                                    <tr id="row-{{ $note->id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $note->patient_name ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($note->note_date)->format('d-m-Y') }}</td>
                                        <td>{{ $note->staff_name }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($note->assess_problem, 30) }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($note->counselling_given, 30) }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($note->remarks, 30) }}</td>

                                        <!-- Actions -->
                                        <td class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-sm btn-info view-note-btn" data-note='@json($note)'>
                                                <i class="fa-solid fa-eye"></i> View
                                            </button>

                                            <a href="{{ route('counsellornotes.edit', $note->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>

                                            <form action="{{ route('counsellornotes.destroy', $note->id) }}" method="POST" style="display:inline;">
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
                <h5 class="modal-title fw-bold" id="viewNoteModalLabel">Counsellor Note Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div id="modalContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<script>
$(document).ready(function() {
    $('#counsellorNotesTable').DataTable({
        pageLength: 10,
        lengthMenu: [4, 8, 12, 16, 20],
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: [7] }], // ✅ fixed index
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
                <div class="col-md-6"><strong>Patient:</strong> ${note.patient_name ?? '-'}</div>
                <div class="col-md-6"><strong>Date:</strong> ${note.note_date}</div>
            </div>
            <div class="mb-2"><strong>Assessed Problem:</strong><br>${note.assess_problem ?? '-'}</div>
            <div class="mb-2"><strong>Counselling Given:</strong><br>${note.counselling_given ?? '-'}</div>
            <div class="mb-2"><strong>Remarks:</strong><br>${note.remarks ?? '-'}</div>
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
