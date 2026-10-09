<?php

session_start();

require_once "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");

    exit;
}

$result = $conn->query(

    "SELECT
        b.*,
        c.customer_name,
        c.meter_number

     FROM electricity_bills b

     INNER JOIN customers c
     ON b.customer_id = c.id

     ORDER BY b.id DESC"

);

?>

<!DOCTYPE html>
<html>

<head>

<title>Electricity Bills</title>

<link rel="stylesheet"
      href="style.css">

</head>

<body>

<?php include "nav.php"; ?>

<div class="container">

<h2>Electricity Bills</h2>

<table>

<tr>

<th>Bill ID</th>

<th>Customer</th>

<th>Meter Number</th>

<th>Month</th>

<th>Energy Charge</th>

<th>Fixed Charge</th>

<th>Total</th>

<th>Action</th>

</tr>

<?php while (
    $row =
    $result->fetch_assoc()
): ?>

<tr>

<td>
<?= $row["id"] ?>
</td>

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
<?= htmlspecialchars(
    $row["billing_month"]
) ?>
</td>

<td>
<?= number_format(
    $row["energy_charge"],
    2
) ?>
RWF
</td>

<td>
<?= number_format(
    $row["fixed_charge"],
    2
) ?>
RWF
</td>

<td>
<strong>
<?= number_format(
    $row["total_amount"],
    2
) ?>
RWF
</strong>
</td>

<td>

<a href="print.php?id=<?= $row["id"] ?>">
Print
</a>

</td>

</tr>

<?php endwhile; ?>

</table>

</div>

</body>

</html>