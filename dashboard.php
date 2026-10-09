<?php

session_start();

require_once "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;
}

$total_customers =
    $conn->query(
        "SELECT COUNT(*) AS total
         FROM customers"
    )->fetch_assoc()["total"];

$total_readings =
    $conn->query(
        "SELECT COUNT(*) AS total
         FROM meter_readings"
    )->fetch_assoc()["total"];

$total_bills =
    $conn->query(
        "SELECT COUNT(*) AS total
         FROM electricity_bills"
    )->fetch_assoc()["total"];

$total_revenue =
    $conn->query(
        "SELECT COALESCE(
            SUM(total_amount), 0
         ) AS total
         FROM electricity_bills"
    )->fetch_assoc()["total"];

?>

<!DOCTYPE html>
<html>

<head>

<title>Dashboard</title>

<link rel="stylesheet"
      href="style.css">

</head>

<body>

<nav>

<div class="logo">
Electricity Billing System
</div>

<a href="dashboard.php" class="active">
Dashboard
</a>

<a href="customer-add.php">
Customers
</a>

<a href="customer-search.php">
Search
</a>

<a href="reading_adds.php">
Meter Reading
</a>

<a href="bill_generate.php">
Generate Bill
</a>

<a href="view_bill.php">
Bills
</a>

<a href="logout.php">
Logout
</a>

</nav>

<div class="container">

<h1>
Welcome,
<?= htmlspecialchars(
    $_SESSION["full_name"]
) ?>
</h1>

<div class="cards">

<div class="card">

<h3>Total Customers</h3>

<h1>
<?= $total_customers ?>
</h1>

</div>

<div class="card">

<h3>Meter Readings</h3>

<h1>
<?= $total_readings ?>
</h1>

</div>

<div class="card">

<h3>Total Bills</h3>

<h1>
<?= $total_bills ?>
</h1>

</div>

<div class="card">

<h3>Total Billing Amount</h3>

<h1>
<?= number_format(
    $total_revenue,
    2
) ?>
</h1>

<p>RWF</p>

</div>

</div>

</div>

</body>

</html>