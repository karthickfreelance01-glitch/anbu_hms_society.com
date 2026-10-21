@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            <h3 class="text-success fw-bold mb-4">Upload Patient Reports</h3>

            <div class="card shadow p-4 border-0">
                <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Patient & Date -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Select Patient</label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">-- Select Patient --</option>
                                @foreach($patients as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Report Date</label>
                            <input type="date" name="report_date" class="form-control" required>
                        </div>
                    </div>

                    <!-- Document Upload Table -->
                    <table class="table table-bordered" id="documentTable">
                        <thead class="table-success text-center">
                            <tr>
                                <th>Document Name</th>
                                <th>Document File</th>
                                <th width="80">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="document_name[]" class="form-control" required></td>
                                <td><input type="file" name="document_file[]" class="form-control" required></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger removeRow">-</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- + Add Row Button (Below Table) -->
                    <div class="text-end mt-2 mb-3">
                        <button type="button" class="btn btn-success" id="addRow">+ Add </button>
                    </div>

                    <!-- Provision Section -->
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Provision / Remarks</label>
                            <textarea name="provision" class="form-control" rows="3" placeholder="Enter any additional notes or provisions here..."></textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-gradient mt-4 px-4">💾 Upload Reports</button>
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
}
</style>

<!-- Script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on('click', '#addRow', function() {
    let row = `
        <tr>
            <td><input type="text" name="document_name[]" class="form-control" required></td>
            <td><input type="file" name="document_file[]" class="form-control" required></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger removeRow">-</button>
            </td>
        </tr>`;
    $('#documentTable tbody').append(row);
});

$(document).on('click', '.removeRow', function() {
    $(this).closest('tr').remove();
});
</script>
@endsection
