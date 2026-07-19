<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: #fff;
            font-size: 2em;
            margin-bottom: 25px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* ── FORM CARD ── */
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }

        .card h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 1.4em;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-weight: 600;
            color: #444;
            margin-bottom: 6px;
            font-size: 0.95em;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
            outline: none;
            font-family: inherit;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #667eea;
        }

        textarea { resize: vertical; min-height: 100px; }

        .radio-group {
            display: flex;
            gap: 20px;
            padding: 10px 0;
        }

        .radio-group label {
            font-weight: 400;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        .radio-group input[type="radio"] {
            width: auto;
            accent-color: #667eea;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s;
            margin-top: 8px;
        }

        .btn-submit:hover  { opacity: 0.92; }
        .btn-submit:active { transform: scale(0.98); }

        /* ── MESSAGES ── */
        .msg {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .msg.success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .msg.error   { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }

        /* ── RECORDS TABLE ── */
        .records-card { background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        .records-card h2 { color: #667eea; margin-bottom: 20px; font-size: 1.4em; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }

        .record-item {
            background: #f8f9ff;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 14px;
            border-left: 4px solid #667eea;
        }

        .record-item p { margin: 4px 0; font-size: 0.95em; color: #444; }
        .record-item p span { font-weight: 600; color: #333; }
        .record-item .record-name { font-size: 1.1em; font-weight: 700; color: #667eea; margin-bottom: 6px; }

        .no-records {
            text-align: center;
            color: #999;
            padding: 30px;
            font-size: 1em;
        }

        .required { color: #dc3545; }
    </style>
</head>
<body>

<div class="container">
    <h1>🎓 Student Registration System</h1>

    <!-- ============================================================
         PHP SIMULATION NOTE:
         Since this runs in the browser (no PHP server),
         this demo uses JavaScript to simulate what PHP would do:
         - Collect $_POST data
         - Validate fields
         - Save to "records.txt" (localStorage here)
         - Read and display all records

         BELOW IS THE ACTUAL PHP CODE you should use on a server.
         ============================================================ -->

    <!-- ── REGISTRATION FORM ── -->
    <div class="card">
        <h2>📝 Register Student</h2>

        <!-- Message area (PHP would echo here) -->
        <div id="messageArea"></div>

        <!--
            PHP CODE:
            <form action="registration.php" method="POST">
        -->
        <form id="registrationForm">

            <!-- Full Name -->
            <div class="form-group">
                <label>Full Name <span class="required">*</span></label>
                <input type="text" name="fullName" id="fullName"
                       placeholder="Enter full name" />
            </div>

            <!-- Email -->
            <div class="form-group">
                <label>Email Address <span class="required">*</span></label>
                <input type="email" name="email" id="email"
                       placeholder="Enter email address" />
            </div>

            <!-- Phone Number -->
            <div class="form-group">
                <label>Phone Number <span class="required">*</span></label>
                <input type="tel" name="phone" id="phone"
                       placeholder="Enter phone number" />
            </div>

            <!-- Gender -->
            <div class="form-group">
                <label>Gender <span class="required">*</span></label>
                <div class="radio-group">
                    <label><input type="radio" name="gender" value="Male" /> Male</label>
                    <label><input type="radio" name="gender" value="Female" /> Female</label>
                    <label><input type="radio" name="gender" value="Other" /> Other</label>
                </div>
            </div>

            <!-- Program -->
            <div class="form-group">
                <label>Program <span class="required">*</span></label>
                <select name="program" id="program">
                    <option value="">-- Select Program --</option>
                    <option value="Software Engineering">Software Engineering</option>
                    <option value="Information Systems">Information Systems</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Big Data">Big Data</option>
                    <option value="Networking">Networking</option>
                </select>
            </div>

            <!-- Message -->
            <div class="form-group">
                <label>Message</label>
                <textarea name="message" id="message"
                          placeholder="Write your message here (optional)..."></textarea>
            </div>

            <button type="submit" class="btn-submit">✅ Submit Registration</button>
        </form>
    </div>

    <!-- ── REGISTERED STUDENTS ── -->
    <div class="records-card">
        <h2>📋 Registered Students</h2>
        <div id="recordsList"></div>
    </div>
</div>


<!-- ============================================================
     JAVASCRIPT: Simulates PHP behaviour in the browser
     On a real PHP server, replace this with the PHP code below
     ============================================================ -->
<script>
    // Key used for localStorage (simulates records.txt)
    const FILE_KEY = 'records_txt';

    // Load and display records on page load
    window.onload = function () {
        displayRecords();
    };

    // Handle form submission
    document.getElementById('registrationForm').addEventListener('submit', function (e) {
        e.preventDefault();

        // Collect $_POST data
        const fullName = document.getElementById('fullName').value.trim();
        const email    = document.getElementById('email').value.trim();
        const phone    = document.getElementById('phone').value.trim();
        const program  = document.getElementById('program').value;
        const message  = document.getElementById('message').value.trim();

        const genderEl = document.querySelector('input[name="gender"]:checked');
        const gender   = genderEl ? genderEl.value : '';

        // PHP equivalent: if(empty($_POST['fullName'])) ...
        if (!fullName || !email || !phone || !gender || !program) {
            showMessage('❌ Please fill in all required fields!', 'error');
            return;
        }

        // Build the record string (same format PHP fwrite() would write)
        const timestamp = new Date().toLocaleString();
        const record =
            "==================================\n" +
            "Full Name : " + fullName  + "\n" +
            "Email     : " + email     + "\n" +
            "Phone     : " + phone     + "\n" +
            "Gender    : " + gender    + "\n" +
            "Program   : " + program   + "\n" +
            "Message   : " + (message || "N/A") + "\n" +
            "Date      : " + timestamp + "\n" +
            "==================================\n";

        // PHP equivalent:
        // $file = fopen("records.txt", "a");   // append mode
        // fwrite($file, $record);
        // fclose($file);
        const existing = localStorage.getItem(FILE_KEY) || '';
        localStorage.setItem(FILE_KEY, existing + record);

        showMessage('✅ Student registered successfully!', 'success');
        document.getElementById('registrationForm').reset();
        displayRecords();
    });

    // PHP equivalent: $data = file_get_contents("records.txt");
    function displayRecords() {
        const data = localStorage.getItem(FILE_KEY) || '';
        const listEl = document.getElementById('recordsList');

        if (!data.trim()) {
            listEl.innerHTML = '<p class="no-records">No students registered yet.</p>';
            return;
        }

        // Split by record separator and build HTML
        const blocks = data.split('==================================\n')
                           .filter(b => b.trim() !== '');

        let html = '';
        blocks.forEach(function (block) {
            const lines = block.split('\n').filter(l => l.trim() !== '');
            if (!lines.length) return;

            const getName = function (key) {
                const line = lines.find(l => l.startsWith(key));
                return line ? line.split(': ').slice(1).join(': ') : 'N/A';
            };

            html +=
                '<div class="record-item">' +
                  '<p class="record-name">👤 ' + getName('Full Name') + '</p>' +
                  '<p>📧 <span>Email:</span> '   + getName('Email')   + '</p>' +
                  '<p>📞 <span>Phone:</span> '   + getName('Phone')   + '</p>' +
                  '<p>⚧ <span>Gender:</span> '   + getName('Gender')  + '</p>' +
                  '<p>🎓 <span>Program:</span> '  + getName('Program') + '</p>' +
                  '<p>💬 <span>Message:</span> '  + getName('Message') + '</p>' +
                  '<p>📅 <span>Date:</span> '     + getName('Date')    + '</p>' +
                '</div>';
        });

        listEl.innerHTML = html || '<p class="no-records">No valid records found.</p>';
    }

    function showMessage(text, type) {
        const area = document.getElementById('messageArea');
        area.innerHTML = '<div class="msg ' + type + '">' + text + '</div>';
        setTimeout(function () { area.innerHTML = ''; }, 4000);
    }
</script>


<!--
====================================================================
ACTUAL PHP CODE (registration.php) - Use this on a real PHP server
====================================================================

<?php

$message = "";
$messageType = "";

// Step 1: Check if form was submitted using POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Step 2: Collect $_POST data
    $fullName = trim($_POST['fullName']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $gender   = trim($_POST['gender']);
    $program  = trim($_POST['program']);
    $message  = trim($_POST['message']);

    // Step 3: Validate - check important fields are not empty
    if (empty($fullName) || empty($email) || empty($phone) ||
        empty($gender)   || empty($program)) {

        $messageType = "error";
        $message     = "Please fill in all required fields!";

    } else {

        // Step 4: Build the record string to save
        $date   = date("Y-m-d H:i:s");
        $record = "==================================\n";
        $record .= "Full Name : " . $fullName . "\n";
        $record .= "Email     : " . $email    . "\n";
        $record .= "Phone     : " . $phone    . "\n";
        $record .= "Gender    : " . $gender   . "\n";
        $record .= "Program   : " . $program  . "\n";
        $record .= "Message   : " . ($message ?: "N/A") . "\n";
        $record .= "Date      : " . $date     . "\n";
        $record .= "==================================\n";

        // Step 5: Open file in APPEND mode "a"
        $file = fopen("records.txt", "a");

        if ($file) {
            // Step 6: Write the record to the file
            fwrite($file, $record);

            // Step 7: Close the file
            fclose($file);

            $messageType = "success";
            $message     = "Student registered successfully!";
        } else {
            $messageType = "error";
            $message     = "Error: Could not open records.txt";
        }
    }
}

// Step 8: Read ALL records from records.txt and display them
$allRecords = "";
if (file_exists("records.txt")) {
    $allRecords = file_get_contents("records.txt");
}
?>

<!-- Display message -->
<?php if ($message): ?>
    <div class="msg <?php echo $messageType; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<!-- Display all records -->
<?php if (!empty($allRecords)): ?>
    <pre><?php echo htmlspecialchars($allRecords); ?></pre>
<?php else: ?>
    <p>No students registered yet.</p>
<?php endif; ?>

====================================================================
-->
</body>
</html>