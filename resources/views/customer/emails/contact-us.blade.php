<!DOCTYPE html>
<html>
<head>
    <title>New Contact Form Submission</title>
</head>
<body>
    @if(!empty($details) && isset($details['store_details']) && !empty($details['store_details']))
        @php $store_details = $details['store_details'][0]; @endphp
        <p>Dear {{ $store_details['store_name'] }},</p>

        <p>You have received a new message from a customer through the contact form on your eCommerce website. Here are the details:</p>
        @if(isset($details['queries']) && !empty($details['queries']))
            <ul>
                <li><strong>Name:</strong> {{ $details['queries']['contactor_name'] }}</li>
                <li><strong>Email:</strong> {{ $details['queries']['contactor_email'] }}</li>
                @if(!empty($details['queries']['contactor_phone_no']))
                    <li><strong>Phone Number:</strong> {{ $details['queries']['contactor_phone_no'] }}</li>
                @endif
                <li><strong>Message:</strong> {{ $details['queries']['contactor_message'] }}</li>
            </ul>
        @endif
        <p>Please respond to this inquiry as soon as possible to assist the customer with their query. Thank you for your prompt attention to this matter.</p>

        <p>Best regards,</p>
        @if(isset($details['queries']) && !empty($details['queries']))
            <p>{{ $details['queries']['contactor_name'] }}</p>
            <p>Email: {{ $details['queries']['contactor_email'] }}</p>
            @if(!empty($details['queries']['contactor_phone_no']))
                <p>Phone:</strong> {{ $details['queries']['contactor_phone_no'] }}</p>
            @endif
        @endif
    @endif
</body>
</html>
