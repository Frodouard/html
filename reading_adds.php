<?php

session_start();

require_once "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");

    exit;
}

$message = "";

$customers =
    $conn->query(
        "SELECT id,
                customer_name,
                meter_number
         FROM customers
         ORDER BY customer_name"
    );

if (!$customers) {
    render_error("Unable to load customers for meter readings.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $customer_id =
        intval(
            $_POST["customer_id"]
        );

    $previous_reading =
        floatval(
            $_POST["previous_reading"]
        );

    $current_reading =
        floatval(
            $_POST["current_reading"]
        );

    $reading_month =
        trim(
            $_POST["reading_month"]
        );

    $reading_date =
        $_POST["reading_date"];

    if (
        $current_reading <
        $previous_reading
    ) {

        $message =
            "Current reading cannot be less than previous reading.";

    } else {

        $consumption =
            $current_reading -
            $previous_reading;

        $stmt = $conn->prepare(
            "INSERT INTO meter_readings
            (
                customer_id,
                previous_reading,
                current_reading,
                consumption,
                reading_month,
                reading_date
            )
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "idddss",
            $customer_id,
            $previous_reading,
            $current_reading,
            $consumption,
            $reading_month,
            $reading_date
        );

        if ($stmt->execute()) {

            $message =
                "Meter reading saved successfully. Consumption: "
                . number_format(
                    $consumption,
                    2
                )
                . " kWh";

        } else {

            $message =
                "Failed to save meter reading.";

        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Meter Reading</title>

<link rel="stylesheet"
      href="style.css">

</head>

<body>

<?php include "nav.php"; ?>

<div class="container">

<h2>Record Monthly Meter Reading</h2>

<?php if ($message): ?>

<div class="message">
<?= htmlspecialchars($message) ?>
</div>

<?php endif; ?>

<form method="POST">

<label>Customer</label>

<select
    name="customer_id"
    required
>

<option value="">
Select Customer
</option>

<?php if ($customers->num_rows === 0): ?>

<option value="" disabled>
No customers available.
</option>

<?php else: ?>

<?php while (
    $customer =
    $customers->fetch_assoc()
): ?>

<option
    value="<?= $customer["id"] ?>"
>

<?= htmlspecialchars(
    $customer["customer_name"]
) ?>

-

<?= htmlspecialchars(
    $customer["meter_number"]
) ?>

</option>

<?php endwhile; ?>

<?php endif; ?>

</select>

<label>
Previous Meter Reading (kWh)
</label>

<input
    type="number"
    name="previous_reading"
    step="0.01"
    min="0"
    required
>

<label>
Current Meter Reading (kWh)
</label>

<input
    type="number"
    name="current_reading"
    step="0.01"
    min="0"
    required
>

<label>Billing Month</label>

<input
    type="month"
    name="reading_month"
    required
>

<label>Reading Date</label>

<input
    type="date"
    name="reading_date"
    value="<?= date('Y-m-d') ?>"
    required
>

<button type="submit">
Save Meter Reading
</button>

</form>

</div>

</body>

</html>