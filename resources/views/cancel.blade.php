
    <title>Payment Cancelled</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #ECEFF1, #CFD8DC); /* soft grey-blue */
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: #ffffff;
            color: #444;
            padding: 40px 50px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.10);
            text-align: center;
            max-width: 420px;
            animation: fadeIn 0.8s ease-out;
        }

        .icon {
            font-size: 64px;
            color: #607D8B; /* soft slate blue */
            margin-bottom: 20px;
        }

        h1 {
            font-size: 26px;
            margin-bottom: 10px;
            font-weight: 700;
            color: #333;
        }

        p {
            font-size: 16px;
            margin-bottom: 25px;
            font-weight: 400;
            color: #555;
        }

        .btn {
            display: inline-block;
            padding: 12px 22px;
            background: #607D8B; /* soft slate */
            color: #fff;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s, transform 0.2s;
        }

        .btn:hover {
            background: #78909C;
            transform: translateY(-2px);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

<div class="card">
    <div class="icon">🛈</div>
    <h1>Payment Cancelled</h1>
    <p>Your checkout session was cancelled. No charges were made.</p>
    <a href="/" class="btn">Return Home</a>
</div>
