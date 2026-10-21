<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Dreams Technologies">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" 
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
</head>

<body>
    <!-- Begin Wrapper -->
    <div class="main-wrapper auth-bg position-relative overflow-hidden">
        <!-- Start Content -->
        <div class="container-fuild position-relative z-1">
            <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">

                <!-- ðŸ”´ Error / Success Messages -->
                <div class="col-lg-4 mx-auto mt-3">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                </div>

                <!-- start row -->
                <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap py-3">
                    <div class="col-lg-4 mx-auto">
                        <form method="POST" action="{{ url('/login') }}"
                            class="d-flex justify-content-center align-items-center">
                            @csrf
                            <div class="d-flex flex-column justify-content-lg-center p-4 p-lg-0 pb-0 flex-fill">
                                <div class="mx-auto mb-4 text-center d-flex align-items-center justify-content-between">
                                    <!-- Left Logo -->
                                    <img src="{{ asset('assets/img/logo.jpeg') }}" alt="Left Logo" class="me-2" height="100">

                                   
                                    
                                </div>
                                <div class="card border-1 p-lg-3 shadow-md rounded-3 mb-4">
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <h5 class="mb-1 fs-20 fw-bold">Sign In</h5>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email Address</label>
                                            <div class="input-group">
                                                <span class="input-group-text border-end-0 bg-white">
                                                    <i class="fa-solid fa-envelope fs-14 text-dark"></i>
                                                </span>
                                                <input type="email" name="email"
                                                    class="form-control border-start-0 ps-0"
                                                    placeholder="Enter Email Address" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <div class="position-relative">
                                                <div
                                                    class="pass-group input-group position-relative border rounded">
                                                    <span class="input-group-text bg-white border-0">
                                                        <i class="fa-solid fa-lock text-dark fs-14"></i>
                                                    </span>
                                                    <input type="password" name="password"
                                                        class="pass-input form-control ps-0 border-0"
                                                        placeholder="****************" required>
                                                    <span class="input-group-text bg-white border-0">
                                                        <i class="fa-solid fa-eye text-dark fs-14"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <button type="submit"
                                                class="btn bg-primary text-white w-100">Login</button>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div>
                        </form>
                        <p class="text-dark text-center">Design & Developed By <b></b>Macnus Solutions</b>.</p>
                    </div><!-- end col -->
                </div>
                <!-- end row -->
            </div>
        </div>
        <!-- End Content -->

        <!-- Start Bg Content -->
        <img src="{{ asset('assets/img/auth/auth-bg-top.png') }}/" alt="" class="img-fluid element-01">
        <img src="{{ asset('assets/img/auth/auth-bg-bot.png') }}/" alt="" class="img-fluid element-02">
        <!-- End Bg Content -->
    </div>
    <!-- End Wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <!-- Bootstrap Core JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Main JS -->
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script>
    $(document).ready(function() {
        $('.fa-eye').on('click', function() {
            let $input = $(this).closest('.pass-group').find('.pass-input');
            
            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $(this).removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                $input.attr('type', 'password');
                $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    });
</script>

</body>

</html>
