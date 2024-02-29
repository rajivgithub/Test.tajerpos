
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>{{ !empty($store_details) && !empty($store_details[0]->store_name) ? $store_details[0]->store_name : 'TajerPOS' }} Login  </title>
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('assets/cashier-admin/imgs/theme/favicon.png') }}" />
    <!-- Template CSS -->
    <link href="{{ URL::asset('assets/css/main.css?v=1.1') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
	<style>
		body,html {
            height: 100%;
        }
	</style>
</head>
<body style="background: #3b3e67;">
	<div class="container-fluid h-100">
        <div class="row justify-content-center align-items-center h-100">
       	    <div class="col-md-4">
	            <div class="shadow" style="background: #e5e6ff;padding: 20px 20px 40px 20px;">
	                <center>
                        <a href="#"><img src="{{ URL::asset('assets/cashier-admin/imgs/theme/login-logo.png') }}" class="logo" alt="eMonta" style="background: #3b3e67;border-radius: 66px;margin-top: -82px;border: 2px solid #e47231;"/></a>
                    </center>
	                <hr/>
                    <form method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.cashier-login') }}" novalidate>
                    @csrf
                        <div>
                            <div class="mb-3 input-field-div">
                                <label class="form-label">User Name</label>
                                <input id="user-email" data-label = "User Name" placeholder="User Name" type="email" class="required-field form-input-field form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>                                
                                @error('email')
                                    <span class="invalid-feedback error-message" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <span class="error error-message"></span>
                            </div>
                            <div class="mb-3 input-field-div">
                                <label class="form-label">Password</label>
                                <input id="user-password" data-label = "Password" placeholder="Password" type="password" class="required-field form-input-field form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback error-message" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <span class="error error-message"></span>
                            </div>
                            <div class="text-center mx-4">
                                <button class="btn btn-md rounded-0 w-100" id="submit-login" style="background:#e07133;margin-top: -23px;display: block;">Login</button>
                            </div>
                        </div>
                    </form>
	            </div>
	        </div>
        </div>
    </div>
	<script src="{{ URL::asset('assets/js/vendors/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/vendors/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/validation.js') }}"></script>
    <script src="{{ URL::asset('assets/js/common.js') }}"></script>
    <script>
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $(document).on("click","#submit-login",function() {
            check_fields = validateFields($(this));
            if(check_fields > 0)
                return false;
            else
                return true;
        });
    </script>
</body>
</html>