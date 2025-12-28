<?php
require_once('../../DATABASE/db.php');
require_once('../../lib/globals.php');
require_once('../../lib/security.php');

// Start session if not already started
if (!isset($_SESSION)) {
    session_start();
}

// Check if user is authorized
authorize();
if (!has_roles(array("Super Admin")) && !has_roles(array("Admin")) && !has_roles(array("Test Administrator")) && !has_roles(array("Test Compositor"))) {
    header("Location: ../../admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Students</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../admin_toolstyle.css" rel="stylesheet">
    <link href="../../assets/css/globalstyle.css" rel="stylesheet">
    <link href="../../assets/css/tconfigstyle.css" rel="stylesheet">
    <link href="../../assets/css/reportstyle.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 20px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .btn-fetch {
            margin-bottom: 20px;
        }
        @media print {
            .print-btn { display: none; }
            .btn-fetch { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Available Students</h2>
        
        <form method="post" action="fetch_student_exec.php">
            
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" name="fetch" class="btn btn-primary" id="continue_btn">Fetch Students</button>
            
        </form>

        <div id="studentTable">
            <!-- Student data will be loaded here -->
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle form submission
            $('form').on('submit', function(e) {
                e.preventDefault();
                loadStudentData();
            });

            // Function to load student data
            function loadStudentData() {
                $.ajax({
                    url: 'fetch_student_exec.php',
                    type: 'POST',
                    data: { fetch: true },
                    success: function(response) {
                        $('#studentTable').html(response);
                    },
                    error: function(xhr, status, error) {
                        $('#studentTable').html('<div class="alert alert-danger">Error loading student data: ' + error + '</div>');
                    }
                });
            }
        });
    </script>
</body>
</html> 