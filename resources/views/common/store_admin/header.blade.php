<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link rel="icon" href="{{ URL::asset('assets/cashier-admin/images/favicon.png') }}">
<title>eMonta Store Admin Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- Vendors Style-->
<link rel="stylesheet" href="{{ URL::asset('assets/cashier-admin/css/vendors_css.css') }}">
<!-- Style-->  
<link rel="stylesheet" href="{{ URL::asset('assets/cashier-admin/css/style.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/cashier-admin/css/skin_color.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link href="{{ URL::asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .profile-image-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: #512DA8;
        font-size: 35px;
        color: #fff;
        text-align: center;
        line-height: 100px;
        margin: 20px 0;
    }
    .default-profile-image {
        border-radius: 50%;
        background: #512DA8;
        color: #fff;
        text-align: center;
        margin: 20px 0;
    }
    .dnone {
        display:none !important;
    }
</style>