<?php

session_start();

require_once "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");

    exit;
}

$message = "";

$readings =
    $conn->query(

        "SELECT
            meter_readings.id,
            meter_readings.consumption,
            meter_readings.reading_month,
            customers.id AS customer_id,
            customers.customer_name,
            customers.meter_number

         FROM meter_readings

         INNER JOIN customers
         ON meter_readings.customer_id =
            customers.id

         ORDER BY meter_readings.id DESC"

    );

if (!$readings) {
    render_error("Unable to load meter readings.");
}

/*
 * SAMPLE ACADEMIC TARIFF
 * Change this according to
 * your lecturer's requirements.
 */

$rate_per_kwh = 200;

$fixed_charge = 500;

$other_charge = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $reading_id =
        intval(
            $_POST["reading_id"]
        );

    $reading_stmt =
        $conn->prepare(

            "SELECT
                mr.*,
                c.id AS customer_id

             FROM meter_readings mr

             INNER JOIN customers c
             ON mr.customer_id = c.id

             WHERE mr.id = ?"

        );

    $reading_stmt->bind_param(
        "i",
        $reading_id
    );

    $reading_stmt->execute();

    $reading =
        $reading_stmt
        ->get_result()
        ->fetch_assoc();

    if (!$reading) {

        $message =
            "Meter reading not found.";

    } else {

        $consumption =
            $reading["consumption"];

        $energy_charge =
            $consumption *
            $rate_per_kwh;

        $total_amount =
            $energy_charge +
            $fixed_charge +
            $other_charge;

        $bill_date =
            date("Y-m-d");

        $billing_month =
            $reading["reading_month"];

        $check =
            $conn->prepare(

                "SELECT id
                 FROM electricity_bills
                 WHERE reading_id = ?"

            );

        $check->bind_param(
            "i",
            $reading_id
        );

        $check->execute();

        $existing =
            $check->get_result();

        if ($existing->num_rows > 0) {

            $message =
                "A bill has already been generated for this reading.";

        } else {

            $stmt = $conn->prepare(

                "INSERT INTO
                 electricity_bills
                (
                    customer_id,
                    reading_id,
                    rate_per_kwh,
                    energy_charge,
                    fixed_charge,
                    other_charge,
                    total_amount,
                    billing_month,
                    bill_date
                )

                VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?)"

            );

            $stmt->bind_param(

                "iiddddsss",

                $reading["customer_id"],
                $reading_id,
                $rate_per_kwh,
                $energy_charge,
                $fixed_charge,
                $other_charge,
                $total_amount,
                $billing_month,
                $bill_date

            );

            if ($stmt->execute()) {

                $bill_id =
                    $conn->insert_id;

                header(
                    "Location: print.php?id="
                    . $bill_id
                );

                exit;

            } else {

                $message =
                    "Failed to generate bill.";

            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Generate Electricity Bill</title>

<link rel="stylesheet"
      href="style.css">

</head>

<body>

<?php include "nav.php"; ?>

<div class="container">

<h2>Generate Electricity Bill</h2>

<div class="message">

Sample academic tariff:

<strong>
<?= number_format(
    $rate_per_kwh
) ?>
RWF per kWh
</strong>

<br>

Fixed charge:
<strong>
<?= number_format(
    $fixed_charge
) ?>
RWF
</strong>

</div>

<?php if ($message): ?>

<div class="error">
<?= htmlspecialchars($message) ?>
</div>

<?php endif; ?>

<form method="POST">

<label>
Select Meter Reading
</label>

<select
    name="reading_id"
    required
>

<option value="">
Select Reading
</option>

<?php if ($readings->num_rows === 0): ?>

<option value="" disabled>
No readings available.
</option>

<?php else: ?>

<?php while (
    $row =
    $readings->fetch_assoc()
): ?>

<option
    value="<?= $row["id"] ?>"
>

<?= htmlspecialchars(
    $row["customer_name"]
) ?>

-

Meter:

<?= htmlspecialchars(
    $row["meter_number"]
) ?>

-

<?= number_format(
    $row["consumption"],
    2
) ?>

kWh -

<?= htmlspecialchars(
    $row["reading_month"]
) ?>

</option>

<?php endwhile; ?>

<?php endif; ?>

</select>

<button type="submit">
Generate Bill
</button>

</form>

</div>

</body>

</html>