<div class="sidebar" id="sidebar">
    <div class="sidebar-logo d-flex align-items-center justify-content-between">
        <a href="{{ url('dashboard') }}" class="d-flex align-items-center text-decoration-none">
            <img src="{{ asset('assets/img/logo.jpeg') }}" alt="Logo" height="55" class="me-2">
            <h6 class="mt-1" style="color:#016b06">Anbu </h6>
        </a>
        <button class="sidenav-toggle-btn btn border-0 p-0" id="toggle_btn">
            <i class="fa-solid fa-bars text-body"></i>
        </button>
        <button class="sidebar-close">
            <i class="fa-solid fa-xmark align-middle"></i>
        </button>
    </div>

    <div class="sidebar-inner" data-simplebar>
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <!-- Main Menu -->
                <li class="menu-title"><span>Main Menu</span></li>
                <li>
                    <a href="{{ url('dashboard') }}">
                        <i class="fa-solid fa-gauge-high"></i><span> Dashboard</span>
                    </a>
                </li>

                <!-- Masters (common for all) -->
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="fa-solid fa-database"></i><span> Masters </span>
                        <span class="menu-arrow"><i class="fa-solid fa-angle-down"></i></span>
                    </a>
                    <ul>
                        <li><a href="{{ url('expenses/view') }}"><i class="fa-solid fa-money-bill-wave"></i> Expense</a></li>
                        <li><a href="{{ url('staff/view') }}"><i class="fa-solid fa-users"></i> Staff</a></li>
                        <li><a href="{{ url('medicine') }}"><i class="fa-solid fa-pills"></i> Medicine</a></li>
                    </ul>
                </li>
                 @if(Auth::user()->role == 'admin')
                <!-- Patient Registration (Visible for both) -->
                <li class="menu-title"><span>Patient Registration</span></li>
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="fa-solid fa-hospital-user"></i><span> Patient Registration</span>
                        <span class="menu-arrow"><i class="fa-solid fa-angle-down"></i></span>
                    </a>
                    <ul>
                        <li><a href="{{ url('patients/create') }}"><i class="fa-solid fa-user-plus"></i> Create Patient</a></li>
                        <li><a href="{{ url('patients') }}"><i class="fa-solid fa-eye"></i> View Patients</a></li>
                        <li><a href="{{ url('patients/discharge/index') }}"><i class="fa-solid fa-bed-pulse"></i> Discharged Patients</a></li>
                    </ul>
                </li>
                
               
                 Patient Billing 
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="fa-solid fa-file-invoice-dollar"></i><span> Patient Bill</span>
                        <span class="menu-arrow"><i class="fa-solid fa-angle-down"></i></span>
                    </a>
                    <ul>
                        <li><a href="{{ url('patient/billing') }}"><i class="fa-solid fa-plus"></i> Create Bill</a></li>
                        <li><a href="{{ url('patient/billing/reports') }}"><i class="fa-solid fa-clipboard-list"></i> View Bills</a></li>
                    </ul>
                </li>

                <li>
                    <a href="{{ url('patient_bills_report') }}">
                        <i class="fa-solid fa-calendar-days"></i><span> Patient Monthly Bill</span>
                    </a>
                </li>
                 <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="fa-solid fa-person-walking-arrow-right"></i><span> Discharge</span>
                        <span class="menu-arrow"><i class="fa-solid fa-angle-down"></i></span>
                    </a>
                    <ul>
                        <li><a href="{{ url('discharge') }}"><i class="fa-solid fa-plus"></i> Create Discharge</a></li>
                        <li><a href="{{ url('patients/discharge/index') }}"><i class="fa-solid fa-clipboard-list"></i> View Discharge</a></li>
                    </ul>
                </li>
                 <!--Expenses (Visible for both) -->
                  <!--Doctor Notes -->
                <!--<li class="menu-title"><span>Patient Registration</span></li>-->
                <!--<li class="submenu">-->
                <!--    <a href="javascript:void(0);">-->
                <!--        <i class="fa-solid fa-hospital-user"></i><span> Patient Registration</span>-->
                <!--        <span class="menu-arrow"><i class="fa-solid fa-angle-down"></i></span>-->
                <!--    </a>-->
                <!--    <ul>-->
                <!--        <li><a href="{{ url('patients/create') }}"><i class="fa-solid fa-user-plus"></i> Create Patient</a></li>-->
                <!--        <li><a href="{{ url('patients') }}"><i class="fa-solid fa-eye"></i> View Patients</a></li>-->
                <!--        <li><a href="{{ url('patients/discharge/index') }}"><i class="fa-solid fa-bed-pulse"></i> Discharged Patients</a></li>-->
                <!--    </ul>-->
                <!--</li>-->
                <!-- <li class="submenu">-->
                <!--    <a href="javascript:void(0);">-->
                <!--        <i class="fa-solid fa-file-invoice-dollar"></i><span> Patient Bill</span>-->
                <!--        <span class="menu-arrow"><i class="fa-solid fa-angle-down"></i></span>-->
                <!--    </a>-->
                <!--    <ul>-->
                <!--        <li><a href="{{ url('patient/billing') }}"><i class="fa-solid fa-plus"></i> Create Bill</a></li>-->
                <!--        <li><a href="{{ url('patient/billing/reports') }}"><i class="fa-solid fa-clipboard-list"></i> View Bills</a></li>-->
                <!--    </ul>-->
                <!--</li>-->

                <!--<li>-->
                <!--    <a href="{{ url('patient_bills_report') }}">-->
                <!--        <i class="fa-solid fa-calendar-days"></i><span> Patient Monthly Bill</span>-->
                <!--    </a>-->
                <!--</li>-->
                <li>
                    <a href="{{url('doctor-notes')}}">
                        <i class="fa-solid fa-notes-medical"></i><span> Doctor Notes</span>
                    </a>
                </li>
                <li>
                    <a href="{{url('counsellor-notes')}}">
                        <i class="fa-solid fa-notes-medical"></i><span> Counsellor Notes</span>
                    </a>
                </li>

                <!-- Discharge -->
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="fa-solid fa-person-walking-arrow-right"></i><span> Discharge</span>
                        <span class="menu-arrow"><i class="fa-solid fa-angle-down"></i></span>
                    </a>
                    <ul>
                        <li><a href="{{ url('discharge') }}"><i class="fa-solid fa-plus"></i> Create Discharge</a></li>
                        <li><a href="{{ url('patients/discharge/index') }}"><i class="fa-solid fa-clipboard-list"></i> View Discharge</a></li>
                    </ul>
                </li>

                <!-- Patient Reports -->
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="fa-solid fa-file-medical"></i><span>Patient Doc Reports</span>
                        <span class="menu-arrow"><i class="fa-solid fa-angle-down"></i></span>
                    </a>
                    <ul>
                        <li><a href="{{url('reports/create')}}"><i class="fa-solid fa-file-circle-plus"></i> Add Reports</a></li>
                        <li><a href="{{url('reports')}}"><i class="fa-solid fa-eye"></i> View Reports</a></li>
                    </ul>
                </li>

                <!-- Expenses (Visible for both) -->
                <!--<li class="menu-title"><span>Expenses</span></li>-->
                <!--<li class="submenu">-->
                <!--    <a href="javascript:void(0);">-->
                <!--        <i class="fa-solid fa-wallet"></i><span> Expenses</span>-->
                <!--        <span class="menu-arrow"><i class="fa-solid fa-angle-down"></i></span>-->
                <!--    </a>-->
                <!--    <ul>-->
                <!--        <li><a href="{{url('expenses/create')}}"><i class="fa-solid fa-plus"></i> Create Expense</a></li>-->
                <!--        <li><a href="{{url('expenses/index')}}"><i class="fa-solid fa-eye"></i> View Expenses</a></li>-->
                <!--    </ul>-->
                <!--</li>-->

                <!-- Reports -->
                <!--<li class="menu-title"><span>Reports</span></li>-->
                <!--<li><a href="{{ url('expenses/reports') }}"><i class="fa-solid fa-chart-line"></i> Expense Reports</a></li>-->
                <li><a href="{{ url('income/reports') }}"><i class="fa-solid fa-chart-line"></i> Income Reports</a></li>
                <li><a href="{{url('doctor-notes-reports')}}"><i class="fa-solid fa-notes-medical"></i> Doctor Notes Reports</a></li>

                <!-- Registers -->
                <li class="menu-title"><span>Registers</span></li>
                <li><a href="{{url('escape_view')}}"><i class="fa-solid fa-person-running"></i> Escape</a></li>
                <li><a href="{{url('death')}}"><i class="fa-solid fa-skull-crossbones"></i> Death</a></li>
                <li><a href="{{url('injuries')}}"><i class="fa-solid fa-hand-holding-medical"></i> Injury</a></li>
                <li><a href="{{url('hearings')}}"><i class="fa-solid fa-file-signature"></i> FIR / CSR</a></li>
                <li><a href="{{url('equipments')}}"><i class="fa-solid fa-stethoscope"></i> Medical Equipment</a></li>
                <li><a href="{{url('dangerously-ill')}}"><i class="fa-solid fa-triangle-exclamation"></i> Danger Illness Record</a></li>

                <!-- Attendance -->
                <li class="menu-title"><span>Attendance</span></li>
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="fa-solid fa-user-check"></i><span> Staff Attendance</span>
                        <span class="menu-arrow"><i class="fa-solid fa-angle-down"></i></span>
                    </a>
                    <ul>
                        <li><a href="{{ url('attendance') }}"><i class="fa-solid fa-calendar-plus"></i> Mark Attendance</a></li>
                        <li><a href="{{ url('attendance/today') }}"><i class="fa-solid fa-eye"></i> Today Report</a></li>
                        <li><a href="{{ url('attendance/date-report') }}"><i class="fa-solid fa-calendar-day"></i> Attendance Report</a></li>
                        <li><a href="{{ url('attendance/mark-date') }}"><i class="fa-solid fa-calendar-plus"></i> Mark by Date</a></li>
                    </ul>
                </li>
                
                 @endif
              
                @if(Auth::user()->role == 'MD')
                <!-- Doctor Notes -->
                <li class="menu-title"><span>Patient Registration</span></li>
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="fa-solid fa-hospital-user"></i><span> Patient Registration</span>
                        <span class="menu-arrow"><i class="fa-solid fa-angle-down"></i></span>
                    </a>
                    <ul>
                        <li><a href="{{ url('patients/create') }}"><i class="fa-solid fa-user-plus"></i> Create Patient</a></li>
                        <li><a href="{{ url('patients') }}"><i class="fa-solid fa-eye"></i> View Patients</a></li>
                        <li><a href="{{ url('patients/discharge/index') }}"><i class="fa-solid fa-bed-pulse"></i> Discharged Patients</a></li>
                    </ul>
                </li>
                 <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="fa-solid fa-file-invoice-dollar"></i><span> Patient Bill</span>
                        <span class="menu-arrow"><i class="fa-solid fa-angle-down"></i></span>
                    </a>
                    <ul>
                        <li><a href="{{ url('patient/billing') }}"><i class="fa-solid fa-plus"></i> Create Bill</a></li>
                        <li><a href="{{ url('patient/billing/reports') }}"><i class="fa-solid fa-clipboard-list"></i> View Bills</a></li>
                    </ul>
                </li>

                <li>
                    <a href="{{ url('patient_bills_report') }}">
                        <i class="fa-solid fa-calendar-days"></i><span> Patient Monthly Bill</span>
                    </a>
                </li>
                <!--<li>-->
                <!--    <a href="{{url('doctor-notes')}}">-->
                <!--        <i class="fa-solid fa-notes-medical"></i><span> Doctor Notes</span>-->
                <!--    </a>-->
                <!--</li>-->
                <!--<li>-->
                <!--    <a href="{{url('counsellor-notes')}}">-->
                <!--        <i class="fa-solid fa-notes-medical"></i><span> Counsellor Notes</span>-->
                <!--    </a>-->
                <!--</li>-->

                <!-- Discharge -->
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="fa-solid fa-person-walking-arrow-right"></i><span> Discharge</span>
                        <span class="menu-arrow"><i class="fa-solid fa-angle-down"></i></span>
                    </a>
                    <ul>
                        <li><a href="{{ url('discharge') }}"><i class="fa-solid fa-plus"></i> Create Discharge</a></li>
                        <li><a href="{{ url('patients/discharge/index') }}"><i class="fa-solid fa-clipboard-list"></i> View Discharge</a></li>
                    </ul>
                </li>

                <!-- Patient Reports -->
                <!--<li class="submenu">-->
                <!--    <a href="javascript:void(0);">-->
                <!--        <i class="fa-solid fa-file-medical"></i><span>Patient Doc Reports</span>-->
                <!--        <span class="menu-arrow"><i class="fa-solid fa-angle-down"></i></span>-->
                <!--    </a>-->
                <!--    <ul>-->
                <!--        <li><a href="{{url('reports/create')}}"><i class="fa-solid fa-file-circle-plus"></i> Add Reports</a></li>-->
                <!--        <li><a href="{{url('reports')}}"><i class="fa-solid fa-eye"></i> View Reports</a></li>-->
                <!--    </ul>-->
                <!--</li>-->

                <!-- Expenses (Visible for both) -->
                <li class="menu-title"><span>Expenses</span></li>
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="fa-solid fa-wallet"></i><span> Expenses</span>
                        <span class="menu-arrow"><i class="fa-solid fa-angle-down"></i></span>
                    </a>
                    <ul>
                        <li><a href="{{url('expenses/create')}}"><i class="fa-solid fa-plus"></i> Create Expense</a></li>
                        <li><a href="{{url('expenses/index')}}"><i class="fa-solid fa-eye"></i> View Expenses</a></li>
                    </ul>
                </li>

                <!-- Reports -->
                <li class="menu-title"><span>Reports</span></li>
                <li><a href="{{ url('expenses/reports') }}"><i class="fa-solid fa-chart-line"></i> Expense Reports</a></li>
                <li><a href="{{ url('income/reports') }}"><i class="fa-solid fa-chart-line"></i> Income Reports</a></li>
                <!--<li><a href="{{url('doctor-notes-reports')}}"><i class="fa-solid fa-notes-medical"></i> Doctor Notes Reports</a></li>-->

                <!-- Registers -->
                <!--<li class="menu-title"><span>Registers</span></li>-->
                <!--<li><a href="{{url('escape_view')}}"><i class="fa-solid fa-person-running"></i> Escape</a></li>-->
                <!--<li><a href="{{url('death')}}"><i class="fa-solid fa-skull-crossbones"></i> Death</a></li>-->
                <!--<li><a href="{{url('injuries')}}"><i class="fa-solid fa-hand-holding-medical"></i> Injury</a></li>-->
                <!--<li><a href="{{url('hearings')}}"><i class="fa-solid fa-file-signature"></i> FIR / CSR</a></li>-->
                <!--<li><a href="{{url('equipments')}}"><i class="fa-solid fa-stethoscope"></i> Medical Equipment</a></li>-->
                <!--<li><a href="{{url('dangerously-ill')}}"><i class="fa-solid fa-triangle-exclamation"></i> Danger Illness Record</a></li>-->

                <!-- Attendance -->
                <!--<li class="menu-title"><span>Attendance</span></li>-->
                <!--<li class="submenu">-->
                <!--    <a href="javascript:void(0);">-->
                <!--        <i class="fa-solid fa-user-check"></i><span> Staff Attendance</span>-->
                <!--        <span class="menu-arrow"><i class="fa-solid fa-angle-down"></i></span>-->
                <!--    </a>-->
                <!--    <ul>-->
                <!--        <li><a href="{{ url('attendance') }}"><i class="fa-solid fa-calendar-plus"></i> Mark Attendance</a></li>-->
                <!--        <li><a href="{{ url('attendance/today') }}"><i class="fa-solid fa-eye"></i> Today Report</a></li>-->
                <!--        <li><a href="{{ url('attendance/date-report') }}"><i class="fa-solid fa-calendar-day"></i> Attendance Report</a></li>-->
                <!--        <li><a href="{{ url('attendance/mark-date') }}"><i class="fa-solid fa-calendar-plus"></i> Mark by Date</a></li>-->
                <!--    </ul>-->
                <!--</li>-->
                @endif
            </ul>
        </div>
    </div>
</div>
    