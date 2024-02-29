
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">  
    <meta name="_token" content="{!! csrf_token() !!}" /> 
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  </head>
  

   

  <style>
  body {
    font-family: Montserrat,sans-serif;
    background: #f5f7f9;
}
  header{
    box-sizing: border-box;
    min-height: 195px;
    background: #7fcaf5;
    background-image: url("{{asset('assets/iconpay/header.png')}}"),linear-gradient(90deg,#7fcaf5 0,#838ff0 100%);
    background-repeat: no-repeat;
    background-position: right bottom;
	}
	.pt_container_inner {
    max-width: 430px;
    margin: 0 auto;
	    text-align: center;
    }
		.pt_merch_logo {
			padding-top: 38px;
		}
		.pt_merch_name {
		margin-top: 5px;
		font-size: 14px;
		color: #fff;
	}
	.pt_container {
    width: 96%;
    max-width: 515px;
    margin: 0 auto;
	margin-top: -60px;
}
.pt_container_inner {
    max-width: 430px;
    margin: 0 auto;
}
.modal-content {
    padding: 21px 21px;
    background: #fff;
    border: 1px solid rgba(0,0,0,.2);
    border-radius: 12px;
    box-shadow: 0 20px 20px #0000001a;
	text-align: center;
}
.modal-content h4
	{
	color: #20bf55;
	}
.modal-content p
   {
    font-size: 17px;
    margin-bottom: 3px;
   }	
   .modal-content .d-flex p 
   {
    font-size: 15px;
	}
.pt_footer {
    margin-top: 40px;
}
.pt_powered_text {
    font-size: 10px;
}
.pt_footer_logo {
    position: relative;
    top: 9px;
    display: inline-block;
    width: 80px;
    height: 23px;
    color: #43424b;
    background-image: url("{{asset('assets/iconpay/paytabs-logo.png')}}");
}
.img-modal-content
  {
    max-width: 64px;
	margin: 0 auto;
  }
  
  </style>

  <body>

    
   <header>
   <div class="container">
     <div class="pt_container_inner">
            <div class="pt_merchant_info"><img class="pt_merch_logo" src="{{asset('assets/iconpay/logo-white-default.png')}}" alt=""></div>
			<!-- <p class="pt_merch_name">Something short and leading about the collection </p> -->
        </div>
	</div>	   
   </header>
  

    <main class="pt_container">
<div class="modal-content mt-4">

  @if($resp['status'] == 'A')
  <h4>Transaction successful</h4>
  <p>Your transaction was completed successfully.</p> 
  <p>{{$resp['ref']}}</p> 
  <img class="img-modal-content" src="{{asset('assets/iconpay/check.png')}}" alt=""> 
  @elseif($resp['status'] == 'C')
  <h4 style="color:#e63946">Transaction Cancelled</h4>
  <p>Your transaction was Cancelled.</p> 
  <p>{{$resp['ref']}}</p> 
  <img class="img-modal-content" src="{{asset('assets/iconpay/cancel.png')}}" alt=""> 
  @elseif($resp['status'] == 'D')
  <h4 style="color:#e63946">Declined</h4>
  <p>{{$resp['message']}}</p> 
  <p>{{$resp['ref']}}</p> 
  <img class="img-modal-content" src="{{asset('assets/iconpay/cancel.png')}}" alt=""> 
  @elseif($resp['status'] == 'E')
  <h4 style="color:#fe646f">Error</h4>
  <p>{{$resp['message']}}</p> 
  <p>{{$resp['ref']}}</p> 
  <img class="img-modal-content" src="{{asset('assets/iconpay/error.png')}}" alt=""> 
  @elseif($resp['status'] == 'X' )
  <h4 style="color:#fe646f">Expired</h4>
  <p>{{$resp['message']}}</p> 
  <p>{{$resp['ref']}}</p> 
  <img class="img-modal-content" src="{{asset('assets/iconpay/error.png')}}" alt=""> 

  @elseif($resp['status'] == 'H')
  <h4 style="color:#ffc048">Hold </h4>
  <p>{{$resp['message']}}</p> 
  <p>{{$resp['ref']}}</p> 
  <img class="img-modal-content" src="{{asset('assets/iconpay/warning.png')}}" alt=""> 
  @elseif($resp['status'] == 'P')
  <h4 style="color:#ffc048">Pending  </h4>
  <p>{{$resp['message']}}</p> 
  <p>{{$resp['ref']}}</p> 
  <img class="img-modal-content" src="{{asset('assets/iconpay/warning.png')}}" alt=""> 
  @elseif($resp['status'] == 'V')
  <h4 style="color:#ffc048">Voided </h4>
  <p>{{$resp['message']}}</p> 
  <p>{{$resp['ref']}}</p> 
  <img class="img-modal-content" src="{{asset('assets/iconpay/warning.png')}}" alt="">
  @else
  <h4 style="color:#fe646f">OOPS Something went wrong... </h4>

  <img class="img-modal-content" src="{{asset('assets/iconpay/error.png')}}" alt="">

  @endif



<hr/> 
<div class="d-flex justify-content-between">
<p>Name</p>
<p>{{$resp['name']}}</p>
</div>
<div class="d-flex justify-content-between">
<p>Amount</p>
<p>SAR {{$resp['amount']}}</p>
</div>
<div class="d-flex justify-content-between">
<p>Account</p>
<p>{{$resp['account']}}</p>
</div>
<div class="d-flex justify-content-between">
<p>Time</p>
<p>{{$resp['time']}}</p>
</div>
<hr>
<div>
  <a href="{{$red_url}}" >Receipt</a>
</div>
</div>



</main>

<footer class="text-muted text-center pt_footer">
    <div class="container">
        <span class="pt_powered_text">Powered By</span>
        <div class="pt_footer_logo"></div>
    </div>
</footer>

 

  
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
