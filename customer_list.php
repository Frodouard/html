<?php

session_start();

require_once "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;
}

$result = $conn->query(
    "SELECT *
     FROM customers
     ORDER BY id DESC"
);

if (!$result) {
    render_error("Unable to load customers at this time.");
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Customers</title>

<link rel="stylesheet"
      href="../css/style.css">

</head>

<body>

<?php include "nav.php"; ?>

<div class="container">

<h2>Electricity Customers</h2>

<table>

<tr>

<th>Customer Code</th>

<th>Name</th>

<th>Phone</th>

<th>Address</th>

<th>Meter Number</th>

<th>Action</th>

</tr>

<?php if ($result->num_rows === 0): ?>

<tr>

<td colspan="6">
No customers found.
</td>

</tr>

<?php else: ?>

<?php while (
    $row = $result->fetch_assoc()
): ?>

<tr>

<td>
<?= htmlspecialchars(
    $row["customer_code"]
) ?>
</td>

<td>
<?= htmlspecialchars(
    $row["customer_name"]
) ?>
</td>

<td>
<?= htmlspecialchars(
    $row["phone"]
) ?>
</td>

<td>
<?= htmlspecialchars(
    $row["address"]
) ?>
</td>

<td>
<strong>
<?= htmlspecialchars(
    $row["meter_number"]
) ?>
</strong>
</td>

<td>

<a href="edit.php?id=<?= $row["id"] ?>">
Update
</a>

</td>

</tr>

<?php endwhile; ?>

<?php endif; ?>

</table>

</div>

</body>

</html>