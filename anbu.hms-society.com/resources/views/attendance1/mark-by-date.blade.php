@extends('layout.admin')
@section('content')

<style>
.page-wrapper { background: #f8f9fc; min-height: 100vh; padding: 20px; }
.card { border-radius: 16px; background: #fff; box-shadow: 0 2px 12px rgba(0,0,0,0.05); }
.card-header { border: none; background: #fff; }
.staff-list { max-height: 500px; overflow-y: auto; }
.staff-item { 
    display: flex; 
    align-items: center; 
    justify-content: space-between; 
    padding: 10px; 
    border-bottom: 1px solid #eee; 
}
.staff-name { font-weight: 500; }
.status-btns button { margin-left: 5px; }

/* SAME COLORS AS YOUR SECOND FILE */
.status-present { background-color: #4caf50; color: #fff; }
.status-absent { background-color: #f44336; color: #fff; }
.status-leave { background-color: #ff9800; color: #fff; }
/* HD styled same way but different color */
.status-halfday { background-color: #2196f3; color: #fff; }

.status-btns button.active { opacity: 0.8; }
</style>

<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">

            <h3 class="mb-4">Mark Attendance</h3>

            <!-- DATE SELECTOR -->
            <form method="GET" class="mb-3">
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="date" name="date" class="form-control" value="{{ $date }}">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Load</button>
                    </div>
                </div>
            </form>

            <!-- ATTENDANCE FORM -->
            <form id="attendanceForm" method="POST" action="{{ route('attendance.mark.date.save') }}">
                @csrf
                <input type="hidden" name="attendance_date" value="{{ $date }}">

                <div class="card staff-list">

                    @foreach($staff as $st)
                        @php $selected = $attendance[$st->id] ?? ""; @endphp

                        <div class="staff-item" data-id="{{ $st->id }}">
                            <span class="staff-name">{{ $st->staff_name }}</span>

                            <div class="status-btns">

                                <button type="button" 
                                    class="btn status-present {{ $selected=='Present'?'active':'' }}" 
                                    data-status="Present">P</button>

                                <button type="button" 
                                    class="btn status-absent {{ $selected=='Absent'?'active':'' }}" 
                                    data-status="Absent">A</button>

                                <button type="button" 
                                    class="btn status-halfday {{ $selected=='Half Day'?'active':'' }}" 
                                    data-status="Half Day">H</button>

                            </div>

                            <input type="hidden" 
                                   name="attendance[{{ $st->id }}]" 
                                   value="{{ $selected }}">
                        </div>

                    @endforeach

                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save Attendance</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){

    $('.status-btns button').click(function(){
        let parent = $(this).closest('.staff-item');

        parent.find('.status-btns button').removeClass('active');
        $(this).addClass('active');

        parent.find('input[type=hidden]').val($(this).data('status'));
    });
    $('#attendanceForm').submit(function(e){
    let isValid = true;

    $('input[type=hidden][name^="attendance"]').each(function(){
        if ($(this).val() === "") {
            isValid = false;
        }
    });

    if (!isValid) {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Incomplete Attendance',
            text: 'Please select P, A, or H for all staff before saving.'
        });

        return false;
    }
});


});
</script>

@endsection
