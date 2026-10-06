<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: #ffffff;
            color: #333;
            padding: 40px 50px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            text-align: center;
            max-width: 420px;
            animation: fadeIn 0.8s ease-out;
        }

        .icon {
            font-size: 64px;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        p {
            font-size: 16px;
            margin-bottom: 25px;
            font-weight: 400;
        }

        .btn {
            display: inline-block;
            padding: 12px 22px;
            background: #4CAF50;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #43A047;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>
<div class="card">
    <div class="icon">✔️</div>
    <h1>Payment Successful!</h1>
    <p>Thank you for your purchase. Your transaction has been completed.</p>
    <a href="/" class="btn">Return Home</a>
</div>
</body>
</html>
