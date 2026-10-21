@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0 container-fluid">

            <!-- Welcome Card -->
            <div class="card text-white rounded-4 shadow-lg mb-5"
                style="background: linear-gradient(135deg, #1e3c72, #2a5298);">
                <div
                    class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 p-4">
                    <div>
                        <h2 class="fw-bold mb-2 text-white">👋 Welcome back, {{ Auth::user()->name ?? 'Admin' }}!</h2>
                        <p class="mb-1 fs-6 fst-italic">“Success is the sum of small efforts repeated day in and day out.” 🌟</p>
                        <p class="text-white-50 mb-0">Here’s a quick overview of your center’s activity.</p>
                    </div>
                    <div class="text-white fw-bold fs-6 text-end" id="currentDateTime"></div>
                </div>
            </div>

            <!-- Dashboard Widgets -->
            <div class="row g-4 justify-content-center">
                 @if(Auth::user()->role == 'admin')
                 @php
                    $widgets = [
                    
                        ['title'=>'Patient Registration','icon'=>'fa-user-plus','url'=>url('patients'),'bg'=>'#6a11cb,#2575fc','tooltip'=>'Create or view patients'],
                        ['title'=>'Income Reports','icon'=>'fa-clock','url'=>url('income/reports'),'bg'=>'#c471f5,#fa71cd','tooltip'=>'Check pending reports'],
                        ['title'=>'Staff Atendance','icon'=>'fa-wallet','url'=>url('attendance/today'),'bg'=>'#ff7e5f,#feb47b','tooltip'=>'Total received payments'],
                        ['title'=>'Add Expense','icon'=>'fa-file-invoice-dollar','url'=>url('expenses/create'),'bg'=>'#43e97b,#38f9d7','tooltip'=>'Add new expense'],
                        ['title'=>'Discharge','icon'=>'fa-hospital-user','url'=>url('patients/discharge/index'),'bg'=>'#ff6a00,#ee0979','tooltip'=>'View discharged patients'],
                        ['title'=>'Patient Doc Reports','icon'=>'fa-file-medical','url'=>url('patients/reports'),'bg'=>'#36d1dc,#5b86e5','tooltip'=>'Patient reports overview'],
                        ['title'=>'Expense Reports','icon'=>'fa-receipt','url'=>url('expenses/reports'),'bg'=>'#f7971e,#ffd200','tooltip'=>'View expense reports'],
                        
                        ];
                        @endphp
                 @endif
                 @if(Auth::user()->role == 'MD')
                @php
                    $widgets = [
                        
                        ['title'=>'Staff Atendance','icon'=>'fa-wallet','url'=>url('attendance/today'),'bg'=>'#ff7e5f,#feb47b','tooltip'=>'Total received payments'],
                        ['title'=>'Add Expense','icon'=>'fa-file-invoice-dollar','url'=>url('expenses/create'),'bg'=>'#43e97b,#38f9d7','tooltip'=>'Add new expense'],
                        ['title'=>'Discharge','icon'=>'fa-hospital-user','url'=>url('patients/discharge/index'),'bg'=>'#ff6a00,#ee0979','tooltip'=>'View discharged patients'],
                        ['title'=>'Patient Doc Reports','icon'=>'fa-file-medical','url'=>url('patients/reports'),'bg'=>'#36d1dc,#5b86e5','tooltip'=>'Patient reports overview'],
                        ['title'=>'Expense Reports','icon'=>'fa-receipt','url'=>url('expenses/reports'),'bg'=>'#f7971e,#ffd200','tooltip'=>'View expense reports'],
                    ];
                @endphp
                @endif
                @foreach($widgets as $widget)
                <div class="col-xl-3 col-md-6">
                    <div class="card shadow-lg border-0 h-100 widget-card"
                        style="background: linear-gradient(135deg, {{ $widget['bg'] }});"
                        data-bs-toggle="tooltip" title="{{ $widget['tooltip'] }}">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <h5 class="text-white mb-0 fw-semibold">{{ $widget['title'] }}</h5>
                            <i class="fa-solid {{ $widget['icon'] }} fa-2x text-white"></i>
                        </div>
                        <div class="card-footer text-center bg-transparent border-0">
                            <a href="{{ $widget['url'] }}" class="btn btn-light btn-sm fw-bold px-3">View</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- DateTime & Tooltip -->
<script>
function updateDateTime() {
    const now = new Date();
    const options = { weekday:'long', year:'numeric', month:'short', day:'numeric', hour:'2-digit', minute:'2-digit', second:'2-digit' };
    document.getElementById('currentDateTime').innerText = now.toLocaleDateString('en-US', options);
}
setInterval(updateDateTime, 1000);
updateDateTime();

// Enable Bootstrap tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>

<style>
.widget-card {
    border-radius: 1rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease, filter 0.3s ease;
    cursor: pointer;
}
.widget-card:hover {
    transform: translateY(-6px) scale(1.03);
    filter: brightness(1.15);
    box-shadow: 0 20px 30px rgba(0,0,0,0.25);
}
.card-footer { border-top: none; }
</style>
@endsection
