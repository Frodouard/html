<?php

session_start();

require_once "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");

    exit;
}

$search =
    trim(
        $_GET["meter_number"] ?? ""
    );

$result = null;

if ($search != "") {

    $stmt = $conn->prepare(
        "SELECT *
         FROM customers
         WHERE meter_number LIKE ?"
    );

    $value =
        "%" . $search . "%";

    $stmt->bind_param(
        "s",
        $value
    );

    try {
        $stmt->execute();

        $result =
            $stmt->get_result();

        if ($result === false) {
            render_error("Search failed. Please try again.");
        }
    } catch (Throwable $e) {
        error_log($e->getMessage());
        render_error("Unable to perform search at this time.");
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Search Customer</title>

<link rel="stylesheet"
      href="style.css">

</head>

<body>

<?php include "nav.php"; ?>

<div class="container">

<h2>Search Customer by Meter Number</h2>

<form method="GET">

<label>
Meter Number
</label>

<input
    type="text"
    name="meter_number"
    placeholder="MTR001"
    value="<?= htmlspecialchars(
        $search
    ) ?>"
    required
>

<button type="submit">
Search
</button>

</form>

<?php if ($result): ?>

<table>

<tr>

<th>Customer Code</th>

<th>Customer Name</th>

<th>Phone</th>

<th>Address</th>

<th>Meter Number</th>

</tr>

<?php if (
    $result->num_rows > 0
): ?>

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

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="5">
No customer found.
</td>

</tr>

<?php endif; ?>

</table>

<?php endif; ?>

</div>

</body>

</html>