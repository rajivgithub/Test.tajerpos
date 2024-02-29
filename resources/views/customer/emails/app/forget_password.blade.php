<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Password Reset Request</title>
    <meta name="description" content="Reset Password">
    <style>
        /* Add your styling here */
        a:hover { text-decoration: underline !important; }
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        h1 {
            color: #1e1e2d;
            font-weight: 500;
            margin: 0;
            font-size: 32px;
            font-family: 'Rubik', sans-serif;
        }
        p {
            color: #455056;
            font-size: 15px;
            line-height: 24px;
            margin: 0;
        }
        .reset-link {
            display: inline-block;
            margin-top: 35px;
            background: #20e277;
            text-decoration: none !important;
            font-weight: 500;
            color: #fff;
            text-transform: uppercase;
            font-size: 14px;
            padding: 10px 24px;
            border-radius: 50px;
        }
        .otp-container {
            background-color: #f2f3f8;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8;" leftmargin="0">
    <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8" style="@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;">
        <tr>
            <td>
                <table style="background-color: #f2f3f8; max-width:670px; margin:0 auto;" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr><td style="height:80px;">&nbsp;</td></tr>
                    <tr>
                        <td style="text-align:center;">
                            <a href="{{ route($data['store_url'].'.customer.home') }}" title="logo" target="_blank">
                                @if(!empty($data) && isset($data['store_details']) && !empty($data['store_details'][0]['store_logo']))
                                    <img width="60" src="{{ $data['store_details'][0]['store_logo'] }}" title="logo" alt="logo">
                                @endif
                            </a>
                        </td>
                    </tr>
                    <tr><td style="height:20px;">&nbsp;</td></tr>
                    <tr>
                        <td>
                            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                                <tr><td style="height:40px;">&nbsp;</td></tr>
                                <tr>
                                    <td style="padding:0 35px;">
                                        <h1>Password Reset Request</h1>
                                        <span style="display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;"></span>
                                        <p>A request to reset your password has been received. To complete the process, please use the following One-Time Password (OTP):</p>
                                        <div class="otp-container">
                                            <strong>Your OTP: {{ $data['token'] }}</strong>
                                        </div>
                                        <p>This OTP is valid for the next 10 minutes. Please do not share it with anyone. If you did not initiate this request, you can safely ignore this email.</p>
                                        <p>Thank you,<br>
                                       {{ (isset($data['store_details']) && !empty($data['store_details'][0]['store_name'])) ? $data['store_details'][0]['store_name'] : "" }}</p>
                                    </td>
                                </tr>
                                <tr><td style="height:40px;">&nbsp;</td></tr>
                            </table>
                        </td>
                    <tr><td style="height:20px;">&nbsp;</td></tr>
                    <tr>
                        <td style="text-align:center;">
                            <p>&copy; <strong></strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:80px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
