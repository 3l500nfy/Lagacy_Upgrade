<?php
require_once('../../DATABASE/db.php');
require_once('../../DATABASE/db_functions.php');
require_once('../../DATABASE/parameters.php');
require_once('../../lib/phpqrcode/qrlib.php');
require_once('../../lib/phpqrcode/qrconfig.php');
require_once('../../lib/phpqrcode/qrtools.php');
require_once('../../lib/phpqrcode/qrspec.php');
require_once('../../lib/phpqrcode/qrimage.php');
require_once('../../lib/phpqrcode/qrinput.php');
require_once('../../lib/phpqrcode/qrbitstream.php');
require_once('../../lib/phpqrcode/qrsplit.php');
require_once('../../lib/phpqrcode/qrrscode.php');
require_once('../../lib/phpqrcode/qrmask.php');
require_once('../../lib/phpqrcode/qrencode.php');
require_once('../../lib/phpqrcode/qrconst.php');
require_once('../../lib/phpqrcode/qrconfig.php');
require_once('../../lib/phpqrcode/qrtools.php');
require_once('../../lib/phpqrcode/qrspec.php');
require_once('../../lib/phpqrcode/qrimage.php');
require_once('../../lib/phpqrcode/qrinput.php');
require_once('../../lib/phpqrcode/qrbitstream.php');
require_once('../../lib/phpqrcode/qrsplit.php');
require_once('../../lib/phpqrcode/qrrscode.php');
require_once('../../lib/phpqrcode/qrmask.php');
require_once('../../lib/phpqrcode/qrencode.php');
require_once('../../lib/phpqrcode/qrconst.php');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../../index.php");
    exit();
}

// Function to fetch and display students
function fetchStudents($dbh) {
    try {
        $query = "SELECT * FROM tblstudents ORDER BY studentid DESC";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($students) > 0) {
            // Start table
            echo '<div class="table-responsive">';
            echo '<table class="table table-striped table-bordered">';
            
            // Table header
            echo '<thead><tr>';
            echo '<th>Student ID</th>';
            echo '<th>Matric Number</th>';
            echo '<th>First Name</th>';
            echo '<th>Last Name</th>';
            echo '<th>Email</th>';
            echo '<th>Phone</th>';
            echo '<th>Department</th>';
            echo '<th>Level</th>';
            echo '<th>Status</th>';
            echo '</tr></thead>';
            
            // Table body
            echo '<tbody>';
            foreach ($students as $student) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($student['studentid']) . '</td>';
                echo '<td>' . htmlspecialchars($student['matricnum']) . '</td>';
                echo '<td>' . htmlspecialchars($student['firstname']) . '</td>';
                echo '<td>' . htmlspecialchars($student['lastname']) . '</td>';
                echo '<td>' . htmlspecialchars($student['email']) . '</td>';
                echo '<td>' . htmlspecialchars($student['phone']) . '</td>';
                echo '<td>' . htmlspecialchars($student['department']) . '</td>';
                echo '<td>' . htmlspecialchars($student['level']) . '</td>';
                echo '<td>' . htmlspecialchars($student['status']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-info">No students found in the database.</div>';
        }
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    }
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Available Students</h2>
        
        <form method="post">
            <button type="submit" name="fetch" class="btn btn-primary btn-fetch">Fetch Students</button>
        </form>

        <?php
        if (isset($_POST['fetch'])) {
            fetchStudents($dbh);
        }
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 