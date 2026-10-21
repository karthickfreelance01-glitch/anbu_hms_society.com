@extends('layout.admin')
@section('content')

<style>
.page-wrapper { background: #f0f2f5; min-height: 100vh; padding: 20px; }
.staff-card { 
    background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
    padding: 15px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;
    transition: transform 0.2s, box-shadow 0.2s; width: 100%;
}
.staff-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
.staff-name { font-weight: 600; font-size: 1rem; color: #1f2937; }
.status-btns { display: flex; gap: 8px; }
.status-btns button { width: 38px; height: 38px; border-radius: 50%; border: none; font-weight: 600; color: #fff; cursor: pointer; transition: all 0.2s; position: relative; }
.status-btns button:hover { transform: scale(1.1); }
.status-btns button.active::after { content: '✔'; position: absolute; right: -5px; bottom: -5px; background: #000; width: 18px; height: 18px; border-radius: 50%; font-size: 12px; text-align: center; line-height: 18px; color: #fff; }
.status-present { background: #10b981; }
.status-absent { background: #ef4444; }
.status-half { background: #3b82f6; }
.staff-list { max-height: 70vh; overflow-y: auto; }
.save-btn { border-radius: 8px; font-weight: 600; padding: 8px 30px; }
.filter-box { margin-bottom: 15px; }
</style>

<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <h3 class="mb-4 text-dark">
                Staff Attendance - {{ \Carbon\Carbon::parse($today)->format('d-m-Y') }}
            </h3>

            <!-- STATIC ROLE LIST -->
            @php
                $roles = [
                    "Accountant", "Admin Staff", "Ambulance Driver", "Cleaner",
                    "Counselor", "Dietician", "Doctor", "HR Manager",
                    "Lab Technician", "Manager", "Nurse", "Pharmacist",
                    "Physiotherapist", "Radiologist", "Receptionist",
                    "Security", "Ward Boy"
                ];
                sort($roles);
            @endphp

            <!-- FILTER SECTION -->
            <div class="row filter-box">
                <div class="col-md-3">
                    <select id="roleFilter" class="form-control">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ strtolower($role) }}">{{ $role }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <input type="text" id="staffSearch" class="form-control" placeholder="Search Staff Name...">
                </div>
            </div>

            <form action="{{ route('attendance.bulkStore') }}" method="POST">
                @csrf
                <input type="hidden" name="attendance_date" value="{{ $today }}">

                <div class="staff-list" id="staffList">
                    @foreach($staff as $st)
                    @php $status = $attendance[$st->id] ?? ''; @endphp

                    <div class="staff-card" 
                        data-staff-id="{{ $st->id }}"
                        data-role="{{ strtolower($st->role) }}">

                        <div>
                            <div class="staff-name">{{ $st->staff_name }}</div>
                            <small class="text-muted">{{ $st->role }}</small>
                        </div>

                        <div class="status-btns">
                            <button type="button" class="status-present {{ $status=='Present' ? 'active' : '' }}" data-status="Present">P</button>
                            <button type="button" class="status-absent {{ $status=='Absent' ? 'active' : '' }}" data-status="Absent">A</button>
                            <button type="button" class="status-half {{ $status=='Half Day' ? 'active' : '' }}" data-status="Half Day">H</button>
                        </div>

                        <input type="hidden" name="attendance[{{ $st->id }}]" value="{{ $status }}">
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary save-btn">Save Attendance</button>
                </div>

            </form>

        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

// Update attendance buttons
$('.status-btns button').click(function(){
    var parent = $(this).closest('.staff-card');
    parent.find('.status-btns button').removeClass('active');
    $(this).addClass('active');
    parent.find('input[type=hidden]').val($(this).data('status'));
});

// Combined filtering (search + role)
function applyFilters() {
    var search = $('#staffSearch').val().toLowerCase();
    var role = $('#roleFilter').val();

    $('#staffList .staff-card').each(function(){
        var name = $(this).find('.staff-name').text().toLowerCase();
        var cardRole = $(this).data('role');

        var matchName = name.includes(search);
        var matchRole = role === "" || role === cardRole;

        $(this).toggle(matchName && matchRole);
    });
}

$('#staffSearch').on('keyup', applyFilters);
$('#roleFilter').on('change', applyFilters);

// Validate before submit
$('form').on('submit', function(e){
    let valid = true;

    $('.staff-card').each(function(){
        const status = $(this).find('input[type=hidden]').val();

        if (!status) {
            valid = false;
            $(this).css('border', '2px solid red');
        } else {
            $(this).css('border', 'none');
        }
    });

    if (!valid) {
        e.preventDefault();
        alert("Please select status (P / A / H) for all staff.");
    }
});

</script>

@endsection
