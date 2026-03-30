<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atlas Insurance - Login</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2563a8 0%, #1e4e8c 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            width: 100%;
            display: flex;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,.3);
        }

        /* LEFT */
        .left-section {
            flex: 1;
            background: linear-gradient(135deg, #eef6fb, #dbeaf5);
            padding: 60px 50px;
            position: relative;
        }

        /* LOGO */
        .logo {
            display: left;
            justify-content: center; /* Center the logo horizontally */
            margin-bottom: 8px; /* Space below the logo */
        }

        .logo-image {
            width: 200px; /* Set appropriate width */
            height: auto; /* Preserve aspect ratio */
        }

        .welcome-text {
            font-size: 32px;
            font-weight: 600;
            color: #1e293b;
            line-height: 1.3;
            margin-bottom: 35px;
        }

        .features {
            list-style: none;
        }

        .feature-item {
            display: flex;
            align-items: center;
            font-size: 18px;
            color: #334155;
            margin-bottom: 20px;
        }

        .feature-icon {
            width: 28px;
            height: 28px;
            margin-right: 14px;
            background: #2563a8;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        /* RIGHT */
        .right-section {
            flex: 1;
            background: #f8fafc;
            padding: 60px 50px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            background: #fff;
            padding: 45px 40px;
            border-radius: 16px;
            box-shadow: 0 6px 30px rgba(0,0,0,.1);
        }

        .login-title {
            text-align: center;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 35px;
            color: #1e293b;
        }

        .input-group {
            margin-bottom: 22px;
        }

        .input-wrapper {
            display: flex;
            align-items: center;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 16px;
            transition: .3s;
        }

        .input-wrapper:focus-within {
            border-color: #2563a8;
        }

        .input-wrapper span {
            font-size: 18px;
            margin-right: 10px;
            color: #94a3b8;
        }

        input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 16px;
        }

        .toggle {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #94a3b8;
        }

        .forgot {
            text-align: right;
            margin-bottom: 22px;
        }

        .forgot a {
            color: #2563a8;
            font-size: 14px;
            text-decoration: none;
        }

        .forgot a:hover {
            text-decoration: underline;
        }

        .btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 8px;
            background: #2563a8;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn:hover {
            background: #1e4e8c;
        }

        .register {
            text-align: center;
            margin-top: 22px;
            font-size: 14px;
            color: #64748b;
        }

        .register a {
            color: #2563a8;
            font-weight: 600;
            text-decoration: none;
        }

        .register a:hover {
            text-decoration: underline;
        }

        @media (max-width: 900px) {
            .container {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <!-- LEFT -->
    <div class="left-section">

        <div class="logo">
            <img src="image/Logo.jpg" alt="Logo" class="logo-image">
        </div>
        
        <div class="welcome-text">
            Welcome to Atlas Insurance.<br>
            Your trusted partner for a<br>
            secure future.
        </div>

        <ul class="features">
            <li class="feature-item"><span class="feature-icon">🛡</span> General Insurance</li>
            <li class="feature-item"><span class="feature-icon">🚗</span> Motor & Travel Cover</li>
            <li class="feature-item"><span class="feature-icon">🔥</span> Marine & Fire Protection</li>
            <li class="feature-item"><span class="feature-icon">🤝</span> Window Takaful Operations</li>
            <li class="feature-item"><span class="feature-icon">AA</span> AA Rated Financial Strength</li>
        </ul>

    </div>

    <!-- RIGHT -->
    <div class="right-section">

        <div class="login-box">
            <div class="login-title">Login to Your Account</div>

            <div class="input-group">
                <div class="input-wrapper">
                    <span>✉</span>
                    <input type="email" placeholder="Email Address">
                </div>
            </div>

            <div class="input-group">
                <div class="input-wrapper">
                    <span>🔒</span>
                    <input type="password" id="pass" placeholder="Password">
                    <button class="toggle" onclick="toggle()">👁</button>
                </div>
            </div>

            <div class="forgot">
                <a href="#">Forgot Password?</a>
            </div>

            <button class="btn">Submit</button>

            <div class="register">
                Don't have an account? <a href="#">Register here</a>
            </div>
        </div>

    </div>

</div>

<script>
    function toggle() {
        const p = document.getElementById('pass');
        p.type = p.type === 'password' ? 'text' : 'password';
    }
</script>

</body>
</html>