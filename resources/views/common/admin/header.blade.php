
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" class="favicon-image" href="{{ URL::asset('assets/imgs/theme/favicon.png') }}" />
    <!-- Template CSS -->
    <link href="{{ URL::asset('assets/css/main.css?v=1.1') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/style-rtl.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="{{ URL::asset('assets/css/responsive.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/page-loader.css') }}" rel="stylesheet" type="text/css" />
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
        .badge-danger {
            background-color: red;
            color: white;
            padding: 4px 6px;
            border-radius: 4px;
        }
        .spinner {
            background-color: #01c293;
        }
    </style>