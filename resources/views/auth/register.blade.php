
<!DOCTYPE html>
<html lang="en">

<head>

	<!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login  Form </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Dreams Technologies">
	
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{asset('assets/img/favicon.png')}}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{asset('assets/img/apple-icon.png')}}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{asset('assets/plugins/tabler-icons/tabler-icons.min.css')}}">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{asset('assets/plugins/simplebar/simplebar.min.css')}}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{asset('assets/plugins/fontawesome/css/fontawesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugins/fontawesome/css/all.min.css')}}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" id="app-style">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper auth-bg position-relative overflow-hidden">

        <!-- Start Content -->
		<div class="container-fuild position-relative z-1">
			<div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">

				<!-- start row -->
				<div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap py-3">
					<div class="col-lg-4 mx-auto">
						<form action="{{ url('register') }}" class="d-flex justify-content-center align-items-center" method="post">
                            @csrf
							<div class="d-flex flex-column justify-content-lg-center p-4 p-lg-0 pb-0 flex-fill">
								<div class=" mx-auto mb-4 text-center">
									<h2>DeAddiction</h2>
								</div>
								<div class="card border-1 p-lg-3 shadow-md rounded-3 mb-4">
									<div class="card-body">
										<div class="text-center mb-3">
											<h5 class="mb-1 fs-20 fw-bold">Register</h5>
											<p class="mb-0">Please enter your details to create account</p>
										</div>
                                        <div class="mb-3">
											<label class="form-label">Full Name</label>
											<div class="input-group">
												<span class="input-group-text border-end-0 bg-white">
													<i class="ti ti-user fs-14 text-dark"></i>
												</span>
												<input type="text" value="" class="form-control border-start-0 ps-0" placeholder="Enter Name" name="name">
											</div>
										</div>
										<div class="mb-3">
											<label class="form-label">Email Address</label>
											<div class="input-group">
												<span class="input-group-text border-end-0 bg-white">
													<i class="ti ti-mail fs-14 text-dark"></i>
												</span>
												<input type="text" value="" class="form-control border-start-0 ps-0" placeholder="Enter Email Address" name="email">
											</div>
										</div>
										<div class="mb-3">
											<label class="form-label">Password</label>
											<div class="position-relative">
                                                <div class="pass-group input-group position-relative border rounded">
                                                    <span class="input-group-text bg-white border-0">
                                                        <i class="ti ti-lock text-dark fs-14"></i>
                                                    </span>
                                                    <input type="password" class="pass-input form-control ps-0 border-0" placeholder="****************" name="password" >
                                                    <span class="input-group-text bg-white border-0">
                                                        <i class="ti toggle-password ti-eye-off text-dark fs-14"></i>
                                                    </span>
                                                </div>
                                            </div>
										</div>
                                        <div class="mb-3">
											<label class="form-label">Confirm Password</label>
											<div class="position-relative">
                                                <div class="pass-group input-group position-relative border rounded">
                                                    <span class="input-group-text bg-white border-0">
                                                        <i class="ti ti-lock text-dark fs-14"></i>
                                                    </span>
                                                    <input type="password" class="pass-inputs form-control ps-0 border-0" placeholder="****************" name="password_confirmation">
                                                    <span class="input-group-text bg-white border-0">
                                                        <i class="ti toggle-passwords ti-eye-off text-dark fs-14"></i>
                                                    </span>
                                                </div>
                                            </div>
										</div>
										<div class="d-flex align-items-center justify-content-between mb-3">
											<div class="d-flex align-items-center">
												<div class="form-check form-check-md mb-0">
													<input class="form-check-input" id="remember_me" type="checkbox">
													<label for="remember_me" class="form-check-label mt-0 text-dark">I agree to the <a href="terms-and-conditions.html" class="text-decoration-underline text-primary"> Terms of Service</a> & <a href="privacy-policy.html" class="text-decoration-underline text-primary">Privacy Policy </a></label>
												</div>
											</div>
										</div>
										<div class="mb-2">
											<button type="submit" class="btn bg-primary text-white w-100">Register</button>
										</div>
										
										<div class="text-center">
											<h6 class="fw-normal fs-14 text-dark mb-0">Already have an account yet? 
												<a href="{{ url('/') }}" class="hover-a"> Login</a>
											</h6>
										</div>
									</div><!-- end card body -->
								</div><!-- end card -->
							</div>
						</form>
						<p class="text-dark text-center">Copyright &copy; 2025 - DeAddiction.</p>
					</div><!-- end col -->
				</div>
				<!-- end row -->
			</div>
		</div>
		<!-- End Content -->

        <!-- Start Bg Content -->

        <img src="{{asset('assets/img/auth/auth-bg-top.png')}}/" alt="" class="img-fluid element-01">
        <img src="{{asset('assets/img/auth/auth-bg-bot.png')}}/" alt="" class="img-fluid element-02">
        <!-- End Bg Content -->

    </div>
    <!-- End Wrapper -->


<!-- jQuery -->
<script src="{{asset('assets/js/jquery-3.7.1.min.js')}}" type="575edcad91077b2fbc2d8d3f-text/javascript"></script>

<!-- Bootstrap Core JS -->
<script src="{{asset('assets/js/bootstrap.bundle.min.js')}}" type="575edcad91077b2fbc2d8d3f-text/javascript"></script>    

<!-- Main JS -->
<script src="{{asset('assets/js/script.js')}}" type="575edcad91077b2fbc2d8d3f-text/javascript"></script>


</body>

</html>