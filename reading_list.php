<?php

session_start();

require_once "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");

    exit;
}

$result = $conn->query(

    "SELECT
        meter_readings.*,
        customers.customer_name,
        customers.meter_number

     FROM meter_readings

     INNER JOIN customers
     ON meter_readings.customer_id =
        customers.id

     ORDER BY meter_readings.id DESC"

);

if (!$result) {
    render_error("Unable to load meter readings.");
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Meter Readings</title>

<link rel="stylesheet"
      href="style.css">

</head>

<body>

<?php include "nav.php"; ?>

<div class="container">

<h2>Monthly Meter Readings</h2>

<table>

<tr>

<th>Customer</th>

<th>Meter Number</th>

<th>Previous Reading</th>

<th>Current Reading</th>

<th>Consumption</th>

<th>Month</th>

<th>Date</th>

</tr>

<?php if ($result->num_rows === 0): ?>

<tr>

<td colspan="7">
No meter readings recorded.
</td>

</tr>

<?php else: ?>

<?php while (
    $row = $result->fetch_assoc()
): ?>

<tr>

<td>
<?= htmlspecialchars(
    $row["customer_name"]
) ?>
</td>

<td>
<?= htmlspecialchars(
    $row["meter_number"]
) ?>
</td>

<td>
<?= number_format(
    $row["previous_reading"],
    2
) ?>
</td>

<td>
<?= number_format(
    $row["current_reading"],
    2
) ?>
</td>

<td>
<strong>
<?= number_format(
    $row["consumption"],
    2
) ?>
kWh
</strong>
</td>

<td>
<?= htmlspecialchars(
    $row["reading_month"]
) ?>
</td>

<td>
<?= $row["reading_date"] ?>
</td>

</tr>

<?php endwhile; ?>

<?php endif; ?>

</table>

</div>

</body>

</html>