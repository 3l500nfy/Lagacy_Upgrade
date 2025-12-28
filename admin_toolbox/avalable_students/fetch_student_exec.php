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

// Check if it's an AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // It's an AJAX request
    if (isset($_POST['fetch'])) {
        try {
            // Modified query to fetch only specified columns
            $query = "SELECT 
                matricnumber,
                surname,
                firstname,
                gender,
                entrysession,
                programmeadmitted
                FROM tblstudents";
            $stmt = $dbh->prepare($query);
            $stmt->execute();
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($students) > 0) {
                // Get column names from the first row
                $columns = array_keys($students[0]);
                
                // Start table with styling from show_summary_print.php
                echo '<div class="print-btn" style="text-align:left;"><a href="javascript:window.print();" id="print-ctr">Print</a></div>';
                echo '<div class="table-responsive">';
                echo '<table id="report-summary-table" class="style-tbl">';
                
                // Table header
                echo '<thead id="report-summary-header"><tr>';
                echo '<th class="f0">S/N</th>';
                foreach ($columns as $column) {
                    echo '<th class="f' . array_search($column, $columns) . '">' . ucwords(str_replace('_', ' ', $column)) . '</th>';
                }
                echo '</tr></thead>';
                
                // Table footer
                echo '<tfoot id="report-summary-footer"><tr>';
                echo '<td class="f0"></td>';
                foreach ($columns as $column) {
                    echo '<td class="f' . array_search($column, $columns) . '"></td>';
                }
                echo '</tr></tfoot>';
                
                // Table body
                echo '<tbody>';
                $rec_count = 1;
                foreach ($students as $student) {
                    echo '<tr class="rec">';
                    echo '<td class="f0">' . $rec_count . '</td>';
                    foreach ($columns as $column) {
                        echo '<td class="f' . array_search($column, $columns) . '">' . htmlspecialchars($student[$column]) . '</td>';
                    }
                    echo '</tr>';
                    $rec_count++;
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
} else {
    // Regular form submission
    if (isset($_POST['fetch'])) {
        try {
            // Modified query to fetch only specified columns
            $query = "SELECT 
                matricnumber,
                surname,
                firstname,
                gender,
                entrysession,
                programmeadmitted
                FROM tblstudents";
            $stmt = $dbh->prepare($query);
            $stmt->execute();
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($students) > 0) {
                // Get column names from the first row
                $columns = array_keys($students[0]);
                
                // Start table with styling from show_summary_print.php
                echo '<div class="print-btn" style="text-align:left;"><a href="javascript:window.print();" id="print-ctr">Print</a></div>';
                echo '<div class="table-responsive">';
                echo '<table id="report-summary-table" class="style-tbl">';
                
                // Table header
                echo '<thead id="report-summary-header"><tr>';
                echo '<th class="f0">S/N</th>';
                foreach ($columns as $column) {
                    echo '<th class="f' . array_search($column, $columns) . '">' . ucwords(str_replace('_', ' ', $column)) . '</th>';
                }
                echo '</tr></thead>';
                
                // Table footer
                echo '<tfoot id="report-summary-footer"><tr>';
                echo '<td class="f0"></td>';
                foreach ($columns as $column) {
                    echo '<td class="f' . array_search($column, $columns) . '"></td>';
                }
                echo '</tr></tfoot>';
                
                // Table body
                echo '<tbody>';
                $rec_count = 1;
                foreach ($students as $student) {
                    echo '<tr class="rec">';
                    echo '<td class="f0">' . $rec_count . '</td>';
                    foreach ($columns as $column) {
                        echo '<td class="f' . array_search($column, $columns) . '">' . htmlspecialchars($student[$column]) . '</td>';
                    }
                    echo '</tr>';
                    $rec_count++;
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
}
?> 