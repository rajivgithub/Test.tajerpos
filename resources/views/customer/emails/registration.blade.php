<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 150px; /* Adjust the width of the logo as needed */
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
        }

        .button {
            text-align: center;
            margin-top: 30px;
        }

        .button a {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            @if(!empty($details) && isset($details['store_details']) && !empty($details['store_details']))
                <img src="{{ $details['store_details'][0]['store_logo'] }}" alt="Company Logo">
            @endif
        </div>
        <div class="message">
            <h2>Thank you for registering with us!</h2>
            <p>Welcome to our {{ !empty($details) && isset($details['store_details']) && !empty($details['store_details']) ? $details['store_details'][0]['store_name'] : "" }} community. Your account has been successfully created.</p>
        </div>
        <div class="button">
            <a href="{{ !empty($details) && isset($details['store_url']) ? route($details['store_url'].'.customer.home') : '' }}" target="_blank">Start Shopping</a>
        </div>
    </div>
</body>
</html>
