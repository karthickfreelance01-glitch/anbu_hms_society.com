@extends('layout.admin')
@section('content')

<style>
.page-wrapper { background: #f0f2f5; min-height: 100vh; padding: 20px; }
.card { background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); padding: 20px; margin-bottom: 20px; }
.card-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; }
.badge { font-weight: 600; padding: 6px 12px; border-radius: 12px; font-size: 0.85rem; }
.badge-success { background: #10b981; color: #fff; }
.badge-danger { background: #ef4444; color: #fff; }
.badge-warning { background: #f59e0b; color: #fff; }
.badge-info { background: #3b82f6; color: #fff; }
.no-record { text-align: center; font-style: italic; color: #888; padding: 20px 0; }
</style>

<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">

            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Attendance History</h4>
                </div>

                <!-- Filters -->
                <form method="GET" class="row g-3 mb-3">
                    <div class="col-md-3">
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="From Date">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="To Date">
                    </div>
                    <div class="col-md-3">
                        <select name="staff_id" class="form-select">
                            <option value="">-- Select Staff --</option>
                            @foreach($staff as $st)
                                <option value="{{ $st->id }}" {{ request('staff_id')==$st->id ? 'selected' : '' }}>
                                    {{ $st->staff_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                        <a href="{{ url('attendance/history') }}" class="btn btn-secondary flex-grow-1">Reset</a>
                    </div>
                </form>

                <!-- Table -->
                <div class="table-responsive">
                    <table id="attendanceTable" class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Staff Name</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendance as $index => $att)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $att->staff_name }}</td>
                                <td>
                                    @php
                                        $statusClass = match($att->status){
                                            'Present' => 'badge badge-success',
                                            'Absent' => 'badge badge-danger',
                                            'Leave' => 'badge badge-warning',
                                            'Half Day' => 'badge badge-info',
                                            default => 'badge bg-secondary'
                                        };
                                    @endphp
                                    <span class="{{ $statusClass }}">{{ $att->status }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($att->attendance_date)->format('d-m-Y') }}</td>
                            </tr>
                            @empty
                            
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables -->
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
        order: [[3, 'desc']],
        responsive: true
    });
});
</script>

@endsection
