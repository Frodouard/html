<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$searchMessage = '';
$editExpense = null;
$results = [];

function sanitize($value) {
    return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $customerName = sanitize($_POST['customer_name'] ?? '');
    $customerPhone = sanitize($_POST['customer_phone'] ?? '');
    $expenseDate = sanitize($_POST['expense_date'] ?? '');
    $expenseDescription = sanitize($_POST['expense_description'] ?? '');
    $amountSpent = sanitize($_POST['amount_spent'] ?? '');
    $paymentMethod = sanitize($_POST['payment_method'] ?? '');

    if ($action === 'reset') {
        header('Location: expenses.php');
        exit;
    }

    if ($action === 'save' || $action === 'update') {
        if ($customerName === '' || $customerPhone === '' || $expenseDate === '' || $expenseDescription === '' || $amountSpent === '' || $paymentMethod === '') {
            $message = 'All fields are required.';
        } elseif (!preg_match('/^[0-9]{7,15}$/', $customerPhone)) {
            $message = 'Customer phone number must contain digits only and be between 7 and 15 digits.';
        } elseif (!is_numeric($amountSpent) || floatval($amountSpent) <= 0) {
            $message = 'Amount spent must be a number greater than 0.';
        } else {
            if ($action === 'save') {
                $stmt = $mysqli->prepare('INSERT INTO expenses (customer_name, customer_phone, expense_date, expense_description, amount_spent, payment_method) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('ssssds', $customerName, $customerPhone, $expenseDate, $expenseDescription, $amountSpent, $paymentMethod);
                if ($stmt->execute()) {
                    $message = 'Expense saved successfully.';
                } else {
                    $message = 'Unable to save expense. Please try again.';
                }
                $stmt->close();
            }

            if ($action === 'update' && isset($_POST['expense_id'])) {
                $expenseId = intval($_POST['expense_id']);
                $stmt = $mysqli->prepare('UPDATE expenses SET customer_name=?, customer_phone=?, expense_date=?, expense_description=?, amount_spent=?, payment_method=? WHERE id=?');
                $stmt->bind_param('ssssdsi', $customerName, $customerPhone, $expenseDate, $expenseDescription, $amountSpent, $paymentMethod, $expenseId);
                if ($stmt->execute()) {
                    $message = 'Expense updated successfully.';
                } else {
                    $message = 'Unable to update expense. Please try again.';
                }
                $stmt->close();
            }
        }
    }

    if ($action === 'search') {
        $searchTerm = sanitize($_POST['search_term'] ?? '');
        $paymentTerm = sanitize($_POST['search_payment_method'] ?? '');

        $sql = 'SELECT * FROM expenses WHERE 1=1';
        $params = [];
        $types = '';

        if ($searchTerm !== '') {
            $sql .= ' AND (customer_phone LIKE ? OR expense_description LIKE ? OR amount_spent = ?)';
            $searchLike = "%{$searchTerm}%";
            if (is_numeric($searchTerm)) {
                $params[] = $searchLike;
                $params[] = $searchLike;
                $params[] = floatval($searchTerm);
                $types .= 'ssd';
            } else {
                $params[] = $searchLike;
                $params[] = $searchLike;
                $params[] = 0;
                $types .= 'ssd';
            }
        }

        if ($paymentTerm !== '') {
            $sql .= ' AND payment_method = ?';
            $params[] = $paymentTerm;
            $types .= 's';
        }

        $stmt = $mysqli->prepare($sql);
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $results = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        if (empty($results)) {
            $searchMessage = 'No matching records were found.';
        }
    }

    if ($action === 'edit' && isset($_POST['expense_id'])) {
        $expenseId = intval($_POST['expense_id']);
        $stmt = $mysqli->prepare('SELECT * FROM expenses WHERE id = ?');
        $stmt->bind_param('i', $expenseId);
        $stmt->execute();
        $result = $stmt->get_result();
        $editExpense = $result->fetch_assoc();
        $stmt->close();
    }
}

$expenseDate = $expenseDate ?? date('Y-m-d');
$customerName = $customerName ?? '';
$customerPhone = $customerPhone ?? '';
$expenseDescription = $expenseDescription ?? '';
$amountSpent = $amountSpent ?? '';
$paymentMethod = $paymentMethod ?? '';
$searchTerm = $_POST['search_term'] ?? '';
$searchPaymentMethod = $_POST['search_payment_method'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Daily Expense Management</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #eef2f6; color: #1d2939; }
        header { background: #1f4bb8; color: white; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; }
        header .brand { font-size: 22px; font-weight: 700; }
        nav { display: flex; gap: 14px; }
        nav a { color: white; text-decoration: none; padding: 10px 14px; border-radius: 8px; background: rgba(255,255,255,0.12); }
        nav a:hover { background: rgba(255,255,255,0.2); }
        main { padding: 24px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; }
        .page-header h1 { margin: 0; font-size: 28px; }
        .panel { background: white; border-radius: 16px; box-shadow: 0 12px 30px rgba(15,23,42,0.08); padding: 24px; margin-bottom: 24px; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; }
        .form-group { display: flex; flex-direction: column; }
        label { margin-bottom: 8px; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 15px; }
        textarea { min-height: 112px; resize: vertical; }
        .button-row { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 16px; }
        button { padding: 12px 18px; border: none; border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 14px; }
        .btn-primary { background: #1f4bb8; color: white; }
        .btn-secondary { background: #475569; color: white; }
        .btn-accent { background: #0ea5e9; color: white; }
        .btn-warning { background: #f59e0b; color: white; }
        .btn-danger { background: #dc2626; color: white; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; font-weight: 700; }
        .message { padding: 14px 18px; border-radius: 12px; background: #f8fafc; color: #0f172a; margin-top: 18px; }
        .message.error { color: #b91c1c; background: #fee2e2; }
        .message.success { color: #164e63; background: #dbeafe; }
        .search-panel { margin-bottom: 24px; }
        .flex-between { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
    </style>
    <script>
        function validateForm() {
            const phone = document.getElementById('customer_phone').value.trim();
            const amount = parseFloat(document.getElementById('amount_spent').value);
            const required = ['customer_name', 'customer_phone', 'expense_date', 'expense_description', 'amount_spent', 'payment_method'];
            for (const field of required) {
                const input = document.getElementById(field);
                if (!input || input.value.trim() === '') {
                    alert('Please fill in all required fields.');
                    input.focus();
                    return false;
                }
            }
            if (!/^[0-9]{7,15}$/.test(phone)) {
                alert('Customer phone number must contain digits only and be between 7 and 15 digits.');
                return false;
            }
            if (isNaN(amount) || amount <= 0) {
                alert('Amount spent must be a number greater than 0.');
                return false;
            }
            return true;
        }

        function printReport() {
            const customerName = document.getElementById('customer_name').value;
            const customerPhone = document.getElementById('customer_phone').value;
            const expenseDate = document.getElementById('expense_date').value;
            const expenseDescription = document.getElementById('expense_description').value;
            const amountSpent = document.getElementById('amount_spent').value;
            const paymentMethod = document.getElementById('payment_method').value;
            const printedDate = new Date().toLocaleString();

            let report = '<html><head><title>Expense Report</title>' +
                '<style>body{font-family:Arial,sans-serif;padding:24px;color:#0f172a;} .report-header{margin-bottom:24px;} .report-header h2{margin:0;} .report-section{margin-bottom:18px;} .report-section strong{display:inline-block;width:180px;}</style>' +
                '</head><body>' +
                '<div class="report-header"><h2>Customer Daily Expense Management</h2><p>Expense Report</p><p>Printed: ' + printedDate + '</p></div>' +
                '<div class="report-section"><h3>Customer Information</h3>' +
                '<p><strong>Name:</strong> ' + customerName + '</p>' +
                '<p><strong>Phone:</strong> ' + customerPhone + '</p>' +
                '</div>' +
                '<div class="report-section"><h3>Expense Record</h3>' +
                '<p><strong>Date:</strong> ' + expenseDate + '</p>' +
                '<p><strong>Description:</strong> ' + expenseDescription + '</p>' +
                '<p><strong>Amount:</strong> ' + amountSpent + '</p>' +
                '<p><strong>Payment Method:</strong> ' + paymentMethod + '</p>' +
                '</div>' +
                '</body></html>';

            const printWindow = window.open('', '_blank', 'width=900,height=700');
            printWindow.document.open();
            printWindow.document.write(report);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }
    </script>
</head>
<body>
    <header>
        <div class="brand">Customer Daily Expense Management</div>
        <nav>
            <a href="#expense-form">Expense Form</a>
            <a href="#search-records">Search Records</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <div class="page-header">
            <div>
                <h1>Expense Management Dashboard</h1>
                <p>Manage customer expense records, search by phone, description, amount, or payment method, and print reports.</p>
            </div>
            <div style="text-align:right; color:#475569; font-size:14px;">Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></div>
        </div>

        <?php if ($message): ?>
            <div class="message <?= strpos($message, 'success') !== false ? 'success' : 'error' ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($searchMessage): ?>
            <div class="message error"><?= htmlspecialchars($searchMessage) ?></div>
        <?php endif; ?>

        <section id="expense-form" class="panel">
            <h2>Expense Record</h2>
            <form method="post" onsubmit="return validateForm();">
                <input type="hidden" name="action" value="<?= $editExpense ? 'update' : 'save' ?>">
                <?php if ($editExpense): ?>
                    <input type="hidden" name="expense_id" value="<?= intval($editExpense['id']) ?>">
                <?php endif; ?>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="customer_name">Customer Name</label>
                        <input type="text" id="customer_name" name="customer_name" value="<?= htmlspecialchars($editExpense['customer_name'] ?? $customerName) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_phone">Customer Phone Number</label>
                        <input type="text" id="customer_phone" name="customer_phone" value="<?= htmlspecialchars($editExpense['customer_phone'] ?? $customerPhone) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="expense_date">Expense Date</label>
                        <input type="date" id="expense_date" name="expense_date" value="<?= htmlspecialchars($editExpense['expense_date'] ?? $expenseDate) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="expense_description">Description of Expense</label>
                        <textarea id="expense_description" name="expense_description" required><?= htmlspecialchars($editExpense['expense_description'] ?? $expenseDescription) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="amount_spent">Amount Spent</label>
                        <input type="number" step="0.01" id="amount_spent" name="amount_spent" value="<?= htmlspecialchars($editExpense['amount_spent'] ?? $amountSpent) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="">Select payment method</option>
                            <option value="Cash" <?= (($editExpense['payment_method'] ?? $paymentMethod) === 'Cash') ? 'selected' : '' ?>>Cash</option>
                            <option value="Mobile Money" <?= (($editExpense['payment_method'] ?? $paymentMethod) === 'Mobile Money') ? 'selected' : '' ?>>Mobile Money</option>
                            <option value="Bank" <?= (($editExpense['payment_method'] ?? $paymentMethod) === 'Bank') ? 'selected' : '' ?>>Bank</option>
                        </select>
                    </div>
                </div>
                <div class="button-row">
                    <button type="submit" class="btn-primary"><?= $editExpense ? 'Update' : 'Save' ?></button>
                    <button type="button" class="btn-accent" onclick="printReport();">Print</button>
                    <button type="submit" class="btn-warning" name="action" value="search">Search</button>
                    <button type="submit" class="btn-danger" name="action" value="reset">Reset</button>
                </div>
            </form>
        </section>

        <section id="search-records" class="panel search-panel">
            <h2>Search Expense Records</h2>
            <form method="post" class="flex-between" style="align-items:flex-end;">
                <input type="hidden" name="action" value="search">
                <div style="flex:1; min-width:220px;">
                    <label for="search_term">Search by phone, description, or amount</label>
                    <input type="text" id="search_term" name="search_term" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Customer phone, expense description, or amount">
                </div>
                <div style="flex:1; min-width:220px;">
                    <label for="search_payment_method">Payment Method</label>
                    <select id="search_payment_method" name="search_payment_method">
                        <option value="">All methods</option>
                        <option value="Cash" <?= $searchPaymentMethod === 'Cash' ? 'selected' : '' ?>>Cash</option>
                        <option value="Mobile Money" <?= $searchPaymentMethod === 'Mobile Money' ? 'selected' : '' ?>>Mobile Money</option>
                        <option value="Bank" <?= $searchPaymentMethod === 'Bank' ? 'selected' : '' ?>>Bank</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn-secondary">Search</button>
                </div>
            </form>

            <?php if (!empty($results)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer Name</th>
                            <th>Phone</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $index => $row): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                <td><?= htmlspecialchars($row['customer_phone']) ?></td>
                                <td><?= htmlspecialchars($row['expense_date']) ?></td>
                                <td><?= htmlspecialchars($row['expense_description']) ?></td>
                                <td><?= number_format($row['amount_spent'], 2) ?></td>
                                <td><?= htmlspecialchars($row['payment_method']) ?></td>
                                <td>
                                    <form method="post" style="margin:0;">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="expense_id" value="<?= intval($row['id']) ?>">
                                        <button type="submit" class="btn-accent" style="padding:8px 12px;">Edit</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
