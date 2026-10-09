<?php

session_start();

require_once "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");

    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $customer_code =
        strtoupper(
            trim(
                $_POST["customer_code"]
            )
        );

    $customer_name =
        trim(
            $_POST["customer_name"]
        );

    $phone =
        trim(
            $_POST["phone"]
        );

    $address =
        trim(
            $_POST["address"]
        );

    $meter_number =
        strtoupper(
            trim(
                $_POST["meter_number"]
            )
        );

    $stmt = $conn->prepare(
        "INSERT INTO customers
        (
            customer_code,
            customer_name,
            phone,
            address,
            meter_number
        )
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "sssss",
        $customer_code,
        $customer_name,
        $phone,
        $address,
        $meter_number
    );

    if ($stmt->execute()) {

        $message =
            "Customer registered successfully.";

    } else {

        $message =
            "Error. Customer code or meter number may already exist.";

    }
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Register Customer</title>

<link rel="stylesheet"
      href="style.css">

</head>

<body>

<?php include "nav.php"; ?>

<div class="container">

<h2>Register Electricity Customer</h2>

<?php if ($message): ?>

<div class="message">
<?= htmlspecialchars($message) ?>
</div>

<?php endif; ?>

<form method="POST">

<label>Customer Code</label>

<input
    type="text"
    name="customer_code"
    placeholder="CUS001"
    required
>

<label>Customer Name</label>

<input
    type="text"
    name="customer_name"
    placeholder="John Doe"
    required
>

<label>Phone Number</label>

<input
    type="tel"
    name="phone"
    placeholder="0780000000"
    required
>

<label>Address</label>

<input
    type="text"
    name="address"
    placeholder="Kigali, Rwanda"
    required
>

<label>Meter Number</label>

<input
    type="text"
    name="meter_number"
    placeholder="MTR001"
    required
>

<button type="submit">
Save Customer
</button>

</form>

</div>

</body>

</html>