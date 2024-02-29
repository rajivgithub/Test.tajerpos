<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>eMonta Store Admin</title>
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
    <body style=" background: rgb(182,25,224);background: linear-gradient(270deg, rgba(182,25,224,1) 0%, rgba(210,135,68,1) 100%);">
	    <div class="container-fluid h-100">
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-md-8">
	                <div class="shadow p-3 bg-white rounded">
	                    <div class="row ">
	                        <div class="col-md-6 d-none d-lg-block">
	                            <div class="h-100 text-center" style="background: rgb(182,25,224);background: linear-gradient(90deg, rgba(182,25,224,1) 0%, rgba(210,135,68,1) 100%);">
	                                <img src="{{ URL::asset('assets/cashier-admin/imgs/login-icon.png') }}" class="img-fluid" alt="eMonta" />
	                            </div>
	                        </div>
	                        <div class="col-md-6">
                                <a href="#"><img src="{{ URL::asset('assets/cashier-admin/imgs/theme/logo.png') }}" class="logo" alt="eMonta" /></a>
	                            <hr/>
                                <form method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.store-login') }}" novalidate>
                                @csrf
                                    <input type="hidden" name="store_url" value="{{$store_url}}">
                                    <div>
                                        <div class="mb-3 input-field-div">
                                            <label class="form-label">User Name</label>
                                            <input id="user-email" data-label = "User Name" placeholder="User Name" type="email" class="required-field form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>                                
                                            @error('email')
                                                <span class="invalid-feedback error-message" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-3 input-field-div">
                                            <label class="form-label">Password</label>
                                            <input id="user-password" data-label = "Password" type="password" placeholder="Password" class="required-field form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                            @error('password')
                                                <span class="invalid-feedback error-message" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <span class="error error-message"></span>
                                        </div>
                                        <button class="btn btn-md rounded font-sm hover-up" id="submit-login" style="background:#d28744">Login</button>
                                    </div>
                                </form>
							</div>
						</div>
	                </div>
	            </div>
            </div>
        </div>
	    <script src="{{ URL::asset('assets/js/vendors/jquery-3.6.0.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/vendors/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/common.js') }}"></script>
        <script src="{{ URL::asset('assets/js/validation.js') }}"></script>
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