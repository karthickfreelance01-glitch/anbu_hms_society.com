@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')
    <div class="page-wrapper">
        <div class="content">
            <h3>Edit Attendance - {{ $record->staff_id }}</h3>
            <form method="POST" action="{{ url('attendance/update/'.$record->id) }}">
                @csrf
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Present" {{ $record->status=='Present'?'selected':'' }}>Present</option>
                    <option value="Absent" {{ $record->status=='Absent'?'selected':'' }}>Absent</option>
                    <option value="Leave" {{ $record->status=='Leave'?'selected':'' }}>Leave</option>
                    <option value="Half Day" {{ $record->status=='Half Day'?'selected':'' }}>Half Day</option>
                </select>
                <button type="submit" class="btn btn-primary mt-2">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
