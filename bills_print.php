<?php

session_start();

require_once "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");

    exit;
}

$id =
    intval(
        $_GET["id"] ?? 0
    );

$stmt = $conn->prepare(

    "SELECT
        b.*,

        c.customer_code,
        c.customer_name,
        c.phone,
        c.address,
        c.meter_number,

        mr.previous_reading,
        mr.current_reading,
        mr.consumption,
        mr.reading_date

     FROM electricity_bills b

     INNER JOIN customers c
     ON b.customer_id = c.id

     INNER JOIN meter_readings mr
     ON b.reading_id = mr.id

     WHERE b.id = ?"

);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$bill =
    $stmt
    ->get_result()
    ->fetch_assoc();

if (!$bill) {

    render_error("Bill not found.", 404);

}

?>

<!DOCTYPE html>
<html>

<head>

<title>
Electricity Bill
</title>

<link rel="stylesheet"
      href="style.css">
</head>

<body>

<div class="bill">

<div class="bill-header">

<h1>
ELECTRICITY BILL
</h1>

<h2>
Electricity Billing Management System
</h2>

<p>
Customer Electricity Statement
</p>

</div>

<div class="bill-info">

<p>
<strong>Bill Number:</strong>
<?= $bill["id"] ?>
</p>

<p>
<strong>Billing Month:</strong>
<?= htmlspecialchars(
    $bill["billing_month"]
) ?>
</p>

<p>
<strong>Bill Date:</strong>
<?= $bill["bill_date"] ?>
</p>

</div>

<hr>

<h3>
Customer Information
</h3>

<table>

<tr>

<td>
<strong>Customer Code</strong>
</td>

<td>
<?= htmlspecialchars(
    $bill["customer_code"]
) ?>
</td>

</tr>

<tr>

<td>
<strong>Customer Name</strong>
</td>

<td>
<?= htmlspecialchars(
    $bill["customer_name"]
) ?>
</td>

</tr>

<tr>

<td>
<strong>Phone</strong>
</td>

<td>
<?= htmlspecialchars(
    $bill["phone"]
) ?>
</td>

</tr>

<tr>

<td>
<strong>Address</strong>
</td>

<td>
<?= htmlspecialchars(
    $bill["address"]
) ?>
</td>

</tr>

<tr>

<td>
<strong>Meter Number</strong>
</td>

<td>
<?= htmlspecialchars(
    $bill["meter_number"]
) ?>
</td>

</tr>

</table>

<h3>
Meter Reading
</h3>

<table>

<tr>

<th>
Previous Reading
</th>

<th>
Current Reading
</th>

<th>
Consumption
</th>

</tr>

<tr>

<td>
<?= number_format(
    $bill["previous_reading"],
    2
) ?>
kWh
</td>

<td>
<?= number_format(
    $bill["current_reading"],
    2
) ?>
kWh
</td>

<td>
<strong>
<?= number_format(
    $bill["consumption"],
    2
) ?>
kWh
</strong>
</td>

</tr>

</table>

<h3>
Bill Calculation
</h3>

<table>

<tr>

<td>
Rate per kWh
</td>

<td>
<?= number_format(
    $bill["rate_per_kwh"],
    2
) ?>
RWF
</td>

</tr>

<tr>

<td>
Energy Charge
</td>

<td>
<?= number_format(
    $bill["energy_charge"],
    2
) ?>
RWF
</td>

</tr>

<tr>

<td>
Fixed Charge
</td>

<td>
<?= number_format(
    $bill["fixed_charge"],
    2
) ?>
RWF
</td>

</tr>

<tr>

<td>
Other Charge
</td>

<td>
<?= number_format(
    $bill["other_charge"],
    2
) ?>
RWF
</td>

</tr>

<tr>

<th>
TOTAL AMOUNT
</th>

<th>
<?= number_format(
    $bill["total_amount"],
    2
) ?>
RWF
</th>

</tr>

</table>

<p class="thank-you">
Thank you for using our electricity billing system.
</p>

<button
    onclick="window.print()"
>
Print Bill
</button>

</div>

</body>

</html>