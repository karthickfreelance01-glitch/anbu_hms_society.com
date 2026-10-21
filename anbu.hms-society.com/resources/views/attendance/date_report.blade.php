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
                <h4>Staff Attendance Report</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Attendance Report</li>
                </ol>
            </div>

            <!-- Filter: Staff + Date Range -->
            <form method="GET" class="g-3 mb-3">
                <div class="card">
                    <div class="card-body row">
                        
                        <div class="col-md-2">
                            <input type="date" name="from_date" class="form-control" value="{{ $from ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="to_date" class="form-control" value="{{ $to ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <select name="role" class="form-select">
                                <option value="">-- Select Role --</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r }}" {{ request('role') == $r ? 'selected' : '' }}>
                                        {{ $r }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <select name="staff_id" class="form-select">
                                <option value="">-- All Staff --</option>
                                @foreach($allStaff as $st)
                                    <option value="{{ $st->id }}" {{ request('staff_id')==$st->id ? 'selected' : '' }}>
                                        {{ $st->staff_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                            <a href="{{ url('attendance/date-report') }}" class="btn btn-secondary flex-grow-1">Reset</a>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Show message if no filter applied -->
            @if($staff->isEmpty())
                <div class="alert alert-info">Please select a staff or date range to view attendance.</div>
            @else
                <!-- Table Card -->
                <div class="card shadow-lg">
                    <div class="card-body table-responsive">
                        <table id="attendanceTable" class="table table-bordered table-striped text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Staff Name</th>
                                    <th>Total Days</th>
                                    @foreach($dates as $date)
                                        <th>{{ \Carbon\Carbon::parse($date)->format('d-m') }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staff as $st)
                                    @php
                                        $totalPresent = 0;
                                        $totalAbsent = 0;
                                        $totalHalfDay = 0;

                                        foreach($dates as $date){
                                            $status = $attendanceRecords[$st->id][$date]->status ?? '';
                                            if($status == 'Present') $totalPresent++;
                                            elseif($status == 'Absent') $totalAbsent++;
                                            elseif($status == 'Half Day') $totalHalfDay++;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $st->staff_name }}</td>
                                        <td>
                                            <span class="badge bg-success">P-{{ $totalPresent }}</span>
                                            <span class="badge bg-danger">A-{{ $totalAbsent }}</span>
                                            <span class="badge bg-primary">HD-{{ $totalHalfDay }}</span>
                                        </td>

                                        @foreach($dates as $date)
                                            @php
                                                $status = $attendanceRecords[$st->id][$date]->status ?? '';
                                                $badgeClass = match($status){
                                                    'Present' => 'badge bg-success',
                                                    'Absent' => 'badge bg-danger',
                                                    'Half Day' => 'badge bg-primary',
                                                    default => ''
                                                };
                                            @endphp
                                           <td>
                                            @if($status)
                                                <span class="{{ $badgeClass }}">{{ $status }}</span>
                                                <button class="btn btn-sm btn-outline-primary editAttendance ms-1"
                                                    data-staff="{{ $st->id }}"
                                                    data-date="{{ $date }}"
                                                    data-status="{{ $status }}" style="padding: 0px 3px;font-size: 13px;">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </button>
                                            @endif
                                        </td>

                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            
            <!-- Edit Attendance Modal -->
<div class="modal fade" id="editAttendanceModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content rounded-4 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Edit Attendance</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="editAttendanceForm">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="staff_id" id="modal_staff_id">
          <input type="hidden" name="attendance_date" id="modal_date">

          <div class="mb-3">
            <label for="modal_status" class="form-label">Status</label>
            <select name="status" id="modal_status" class="form-select" required>
              <option value="">-- Select Status --</option>
              <option value="Present">Present</option>
              <option value="Absent">Absent</option>
              <option value="Half Day">Half Day</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save Changes</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>


        </div>
    </div>
</div>
</div>

<!-- jQuery & DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- DataTables Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    $('#attendanceTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        dom: "<'row mb-3'<'col-sm-6'f><'col-sm-6 text-end'B>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-primary' },
            { extend: 'csv', className: 'btn btn-sm btn-success' },
            { extend: 'excel', className: 'btn btn-sm btn-info' },
            { extend: 'pdf', className: 'btn btn-sm btn-danger' },
            { extend: 'print', className: 'btn btn-sm btn-warning' }
        ],
        responsive: true
    });
});

$(document).ready(function() {
    // Existing DataTable code here...

    // Open Edit Modal
    $(document).on('click', '.editAttendance', function() {
        const staffId = $(this).data('staff');
        const date = $(this).data('date');
        const status = $(this).data('status');

        $('#modal_staff_id').val(staffId);
        $('#modal_date').val(date);
        $('#modal_status').val(status);
        $('#editAttendanceModal').modal('show');
    });

    // Handle form submit
    $('#editAttendanceForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ url('attendance/update-status') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    alert('Attendance updated successfully!');
                    $('#editAttendanceModal').modal('hide');
                    location.reload(); // Refresh the page to update the status
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Something went wrong!');
            }
        });
    });
});

</script>

@endsection
