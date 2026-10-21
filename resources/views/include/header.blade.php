<header class="navbar-header">
    <div class="page-container topbar-menu">
        <div class="d-flex align-items-center gap-2">

            <!-- Logo -->
            <a href="{{ url('dashboard') }}" class="logo">

                <!-- Logo Normal -->
                <span class="logo-light">
                    <span class="logo-lg"><img src="{{ asset('img/logo.svg') }}" alt="logo"></span>
                    <span class="logo-sm"><img src="{{ asset('img/logo-small.svg') }}" alt="small logo"></span>
                </span>

                <!-- Logo Dark -->
                <span class="logo-dark">
                    <span class="logo-lg"><img src="{{ asset('img/logo-white.svg') }}" alt="dark logo"></span>
                </span>
            </a>

            <!-- Sidebar Mobile Button -->
            <a id="mobile_btn" class="mobile-btn" href="#sidebar">
                <i class="fa-solid fa-bars fs-24"></i>
            </a>

            <button class="sidenav-toggle-btn btn border-0 p-0 active" id="toggle_btn2"> 
                <i class="ti ti-arrow-right"></i>
            </button>
        </div>

        <div class="d-flex align-items-center">

            <!-- Appointment -->
            <div class="header-item">
                <!-- Optional: Appointment button -->
            </div>
            <!-- /Appointment -->

            <!-- User Dropdown -->
            <div class="dropdown profile-dropdown d-flex align-items-center justify-content-center">
                <a href="javascript:void(0);" class="topbar-link dropdown-toggle drop-arrow-none position-relative" 
                    data-bs-toggle="dropdown" data-bs-offset="0,22" aria-haspopup="false" aria-expanded="false">
                    <img src="{{ asset('assets/img/logo.jpeg') }}" width="52" class="rounded-circle d-flex" alt="user-image">
                    <span class="online text-success">
                        <i class="fa-solid fa-user-gear d-flex bg-white rounded-circle border border-1 border-white"></i>
                    </span>
                </a>

                <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                    <a href="javascript:void(0);" class="dropdown-item text-danger fw-semibold" id="logoutBtn">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                    </a>
                </div>
            </div>
            <!-- /User Dropdown -->
        </div>
    </div>

    <!-- Hidden Logout Form -->
    <form id="logoutForm" action="{{ url('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    
</header>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const logoutBtn = document.getElementById('logoutBtn');
    const logoutForm = document.getElementById('logoutForm');

    if (logoutBtn) {
        logoutBtn.addEventListener('click', function () {
            Swal.fire({
                title: 'Are you sure you want to log out?',
                text: 'You will need to log in again to continue.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, log out',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    logoutForm.submit();
                }
            });
        });
    }
});
</script>
