<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        .maincontainer {
            width: 600px;
            margin: 30px auto;
            font-family: Arial, sans-serif;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background-color: #f9f9f9;
            text-align: left;
            /* Ensure left alignment */
        }

        .card-header {
            text-align: center;
        }

        .card-header img {
            width: 150px;
        }

        .card-body,
        .card-footer {
            text-align: left;
            padding: 0 20px;
            /* Ensures both have the same padding */
        }

        .otp-code {
            font-size: 20px;
            font-weight: bold;
            background: #eee;
            padding: 10px;
            display: inline-block;
            border-radius: 5px;
            margin: 10px 0;
        }

        .card-footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 14px;
            color: #555;
        }

        .footer-text {
            margin: 0;
            /* Ensures alignment consistency */
        }
    </style>
</head>

<body>
<div class="maincontainer">
    <div class="card-header">
        <img src="https://dtzxzoe2ldz0i.cloudfront.net/SuxxrXOrJY3FLfnnzDXOHqJVeRJ05ycDBwDENbGS.png" alt="Logo">
    </div>
    <div class="card-body">
        <h3>Login OTP for Authentication</h3>
        <p>We have sent you this email to verify your identity and complete your login process.</p>
        <p>Hi,</p>
        <p>Your one-time password (OTP) for logging into your account is:</p>
        <div class="otp-code">{{ $emailData['otp'] }}</div>
        <p>The above OTP is valid for 5 minutes.</p>
        <p>If you did not request to log in, you can safely ignore this email.</p>
        </div>
        <div class="card-footer">
            <p>Regards,</p>
            <p class="footer-text">
                <strong>
                        Copyright© {{ now()->year }} - Sanjeevani® All Rights Reserved.
                </strong>
        </div>
    </div>
</body>

</html>
