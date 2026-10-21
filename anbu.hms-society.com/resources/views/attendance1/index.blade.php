@extends('layout.admin')
@section('content')
<style>
.page-wrapper { background: #f8f9fc; min-height: 100vh; padding: 20px; }
.card { border-radius: 16px; background: #fff; box-shadow: 0 2px 12px rgba(0,0,0,0.05); }
.card-header { border: none; background: #fff; }
.staff-list { max-height: 500px; overflow-y: auto; }
.staff-item { display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid #eee; }
.staff-name { font-weight: 500; }
.status-btns button { margin-left: 5px; }
.status-present { background-color: #4caf50; color: #fff; }
.status-absent { background-color: #f44336; color: #fff; }
.status-leave { background-color: #ff9800; color: #fff; }
.status-btns button.active { opacity: 0.8; }
</style>

<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <h3 class="mb-4">Staff Attendance - {{ \Carbon\Carbon::now()->format('d-m-Y') }}</h3>

            <form id="attendanceForm">
                @csrf
                <input type="hidden" name="attendance_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                <div class="card staff-list">
                    @foreach($staff as $st)
                        <div class="staff-item" data-staff-id="{{ $st->id }}">
                            <span class="staff-name">{{ $st->staff_name }}</span>
                            <div class="status-btns">
                                <button type="button" class="btn status-present" data-status="Present">P</button>
                                <button type="button" class="btn status-absent" data-status="Absent">A</button>
                                <button type="button" class="btn status-leave" data-status="Leave">L</button>
                            </div>
                            <input type="hidden" name="attendance[{{ $st->id }}]" value="">
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

    // Handle status button click
    $('.status-btns button').click(function(){
        var parent = $(this).closest('.staff-item');
        parent.find('.status-btns button').removeClass('active');
        $(this).addClass('active');
        parent.find('input[type=hidden]').val($(this).data('status'));
    });

    // Submit attendance form
    $('#attendanceForm').submit(function(e){
        e.preventDefault();

        // ---------- REQUIRED VALIDATION ----------
        let allFilled = true;

        $('input[type="hidden"][name^="attendance"]').each(function(){
            if ($(this).val().trim() === "") {
                allFilled = false;
            }
        });

        if (!allFilled) {
            Swal.fire({
                icon: 'warning',
                title: 'Attendance Not Complete',
                text: 'Please select P, A, or L for all staff.'
            });
            return false;
        }
        // -----------------------------------------

        // Submit if all selected
        $.post("{{ url('attendance/bulk') }}", $(this).serialize(), function(){
            Swal.fire("Saved!", "Attendance has been recorded.", "success").then(()=> location.reload());
        }).fail(()=>{
            Swal.fire("Error!", "Failed to save attendance.", "error");
        });
    });

});
</script>

@endsection
