@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard UI</title>

<style>

/* RESET */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

/* LAYOUT */
body {
    display: flex;
    background: #f5f7fb;
}

/* SIDEBAR */
.sidebar {
    width: 240px;
    height: 100vh;
    background: #0f172a;
    color: #fff;
    position: fixed;
    padding: 20px;
}

.sidebar h2 {
    margin-bottom: 30px;
}

.sidebar ul {
    list-style: none;
}

.sidebar ul li {
    padding: 12px 10px;
    margin-bottom: 10px;
    border-radius: 8px;
    cursor: pointer;
}

.sidebar ul li:hover {
    background: #1e293b;
}

.sidebar ul li.active {
    background: #2563eb;
}

/* MAIN CONTENT */
.main {
    margin-left: 240px;
    width: 100%;
    display: flex;
    flex-direction: column;
}

/* NAVBAR */
.navbar {
    height: 60px;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    border-bottom: 1px solid #ddd;
}

.navbar input {
    padding: 8px 12px;
    width: 250px;
    border: 1px solid #ddd;
    border-radius: 6px;
}

.navbar .right {
    display: flex;
    gap: 15px;
    align-items: center;
}

.navbar .btn {
    background: #2563eb;
    color: #fff;
    padding: 8px 14px;
    border-radius: 6px;
    cursor: pointer;
}

/* CONTENT */
.content {
    padding: 20px;
    flex: 1;
}

/* CARDS */
.cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.card {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.card h4 {
    color: #666;
    margin-bottom: 10px;
}

.card h2 {
    margin-bottom: 5px;
}

.green { color: green; }
.red { color: red; }

/* CHART BOX */
.chart-box {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    height: 250px;
}

/* FOOTER */
.footer {
    background: #fff;
    padding: 15px;
    text-align: center;
    border-top: 1px solid #ddd;
}

</style>

</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>⚡ Apex</h2>

    <ul>
        <li class="active">Dashboard</li>
        <li>Analytics</li>
        <li>eCommerce</li>
        <li>CRM</li>
        <li>SaaS</li>
        <li>Orders</li>
        <li>Products</li>
        <li>Customers</li>
    </ul>
</div>


<div class="main">

    <!-- NAVBAR -->
    <div class="navbar">
        <input type="text" placeholder="Search...">

        <div class="right">
            <div class="btn">+ New Order</div>
            <span>🔔</span>
            <span>👤</span>
            <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button class="btn btn-danger">Logout</button>
</form>

        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <h2 style="margin-bottom:15px;">Dashboard</h2>

        <!-- CARDS -->
        <div class="cards">

            <div class="card">
                <h4>Total Revenue</h4>
                <h2>$48,295</h2>
                <span class="green">+12.5%</span>
            </div>

            <div class="card">
                <h4>Active Users</h4>
                <h2>2,847</h2>
                <span class="green">+8.2%</span>
            </div>

            <div class="card">
                <h4>Total Orders</h4>
                <h2>1,432</h2>
                <span class="red">-3.1%</span>
            </div>

            <div class="card">
                <h4>Page Views</h4>
                <h2>284K</h2>
                <span class="green">+24.7%</span>
            </div>

        </div>

        <!-- CHART -->
        <div class="chart-box">
            <h3>Overview (Chart Placeholder)</h3>
        </div>

    </div>

    <!-- FOOTER -->
    <div class="footer">
        © 2026 Your Dashboard. All rights reserved.
    </div>

</div>

</body>
</html>


@endsection
