<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructions</title>
    <link rel="stylesheet" href="css/font.css">
    <style>
        body {
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h2 {
            color: #000000;
            margin-top: 0px;
        }

        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
        }

        /* Step Section */
        .step {
            border-left: 5px solid #1d4ed8;
            padding: 15px;
            margin: 20px 0;
            position: relative;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .step h3 {
            margin: 0;
            font-size: 20px;
            color: #1d4ed8;
        }

        .dates {
            font-size: 16px;
            color: #555;
            margin-top: 5px;
        }

        .instructions {
            margin-top: 10px;
            font-size: 14px;
            color: #333;
        }

        @media (max-width: 600px) {
            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
<?php
include 'part/header.php';
?>
    <div class="container">
        <h2>Instructions</h2>
        <!-- Step 0 -->
        <div class="step">
          <h3>Step 1: Fee Payment</h3>
          <p class="dates">Opening Date: March 1, 2025<br>Closing Date: March 15, 2025</p>
          <p class="instructions">
            - Pay ₹50 to register an individual.<br>
            - Pay via UPI and always mention applicant's and his father's name in notes.<br>
            - Send payment and form screenshot to this number after successful registration.
          </p>
        </div>
        
        <!-- Step 1 -->
        <div class="step">
            <h3>Step 2: Registration</h3>
            <p class="dates">Opening Date: March 1, 2025<br>Closing Date: March 15, 2025</p>
            <p class="instructions">
                - Ensure your personal details are accurate.<br>
                - Upload a recent formal image (Max 100KB, JPG/PNG) and 512x512 pixels.<br>
                - Fill all required fields before submission.
            </p>
        </div>
        <!-- Step 2 -->
        <div class="step">
            <h3>Step 3: Print Registration Details</h3>
            <p class="dates">Available from: March 1, 2025</p>
            <p class="instructions">
                - Verify your registration details before printing.<br>
                - Keep a copy for future reference.<br>
                - If you find any errors, contact support immediately.
            </p>
        </div>

        <!-- Step 3 -->
        <div class="step">
            <h3>Step 4: Admit Cards</h3>
            <p class="dates">Opening Date: March 20, 2025<br>Closing Date: April 10, 2025</p>
            <p class="instructions">
                - Download and print your admit card.<br>
                - Carry a valid ID along with the admit card to the exam center.<br>
                - Ensure the details match your registration form.
            </p>
        </div>

        <!-- Step 4 -->
        <div class="step">
            <h3>Step 5: Results</h3>
            <p class="dates">Expected Release: April 10, 2025</p>
            <p class="instructions">
                - Check results using your registration number and DOB.<br>
                - If there are discrepancies, report them immediately.<br>
                - Print your result for future use.
            </p>
        </div>
    </div>
<?php
include 'part/footer.php';
?>
</body>
</html>