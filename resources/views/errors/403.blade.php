<!DOCTYPE html>
<html>
<head>
    <title>Access Denied</title>
    <style>
        body {
            text-align: center;
            font-family: Arial;
            background: #f8f9fa;
        }
        .box {
            margin-top: 100px;
        }
        h1 {
            font-size: 80px;
            color: red;
        }
        p {
            font-size: 20px;
        }
        a {
            text-decoration: none;
            padding: 10px 20px;
            background: black;
            color: white;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="box">
    <h1>403</h1>
    <p>Access Denied 🚫</p>

    <a href="{{ url('/') }}">Go Home</a>
</div>

</body>
</html>
