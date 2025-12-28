<?php
// Content-Security-Policy-Report-Only header (Phase B5.3)
// Send CSP header before any output to observe violations without breaking functionality
if (!headers_sent()) {
    header("Content-Security-Policy-Report-Only: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline' data:; img-src 'self' data:; font-src 'self' data:; connect-src 'self'; frame-ancestors 'self'; base-uri 'self'; form-action 'self'; report-uri /CBTTEST1/csp-report-endpoint.php");
}

$app['default']['root_folder'] = "CBTTEST1";

if(!isset($dbh) || !($dbh instanceof PDO)) {
    $dbh = null;
}

// Define database connection variables globally
global $hostname_cnpgdataport, $database_cnpgdataport, $username_cnpgdataport, $password_cnpgdataport;
$hostname_cnpgdataport = "localhost";
$database_cnpgdataport = "dlc_cbtconverted";
$username_cnpgdataport = "root";
$password_cnpgdataport = "";

if(!function_exists("openConnection")) {
    function openConnection($persistent=false)
    {
        global $dbh, $hostname_cnpgdataport, $database_cnpgdataport, $username_cnpgdataport, $password_cnpgdataport;

        if ($dbh != null) {
            return $dbh;
        }
   
        try {
            if ($persistent) {
                $dbh = new PDO("mysql:host=$hostname_cnpgdataport;dbname=$database_cnpgdataport", $username_cnpgdataport, $password_cnpgdataport, array(PDO::ATTR_PERSISTENT => true));
            } else {
                $dbh = new PDO("mysql:host=$hostname_cnpgdataport;dbname=$database_cnpgdataport", $username_cnpgdataport, $password_cnpgdataport);
            }
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbh;
        } catch (PDOException $e) {
            echo "<div style='margin: 20px; padding: 20px; border: 1px solid #ff0000; background-color: #ffeeee;'>";
            echo "<h2>Database Connection Error</h2>";
            echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
            echo "<p><strong>Code:</strong> " . $e->getCode() . "</p>";
            echo "<p>Please check the following:</p>";
            echo "<ul>";
            echo "<li>MySQL service is running in XAMPP</li>";
            echo "<li>Database 'dlc_cbtconverted' exists</li>";
            echo "<li>Database credentials are correct</li>";
            echo "</ul>";
            echo "</div>";
            die();
        }
    }
}

function siteUrl($scriptFile="") {
    global $app;
    
    $baseUrl = "http://" . $_SERVER['SERVER_NAME'];
    
    if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') {
        $baseUrl .= ":" . $_SERVER['SERVER_PORT'];
    }
    
    $path = $app['default']['root_folder'];
    if (!empty($scriptFile)) {
        $path .= "/" . ltrim($scriptFile, "/");
    }
    
    return $baseUrl . "/" . $path;
}

function pageTitle($title) {
    return $title . "CBT";
}

function redirectTo($url) {
    header("Location: " . $url);
    exit();
}

function redirect($url) {
    redirectTo($url);
}

function clean($str) {
    if ($str === null) {
        return '';
    }
    $str = trim($str);
    return stripslashes($str);
}

function absolutePath($path) {
    return realpath(dirname(__FILE__) . $path);
}

function sanitizeInput($str) {
    if ($str === null) {
        return '';
    }
    $str = trim($str);
    return htmlspecialchars(stripslashes($str), ENT_QUOTES, 'UTF-8');
}

function daydiff($from, $to) {
    $diff = strtotime($from) - strtotime($to) + 1;
    $day_difference = ceil($diff / (60 * 60 * 24));
    return $day_difference;
}

function formatDateToDDMMYYY($str) {
    $date_arr = explode("-", $str);
    $date = $date_arr[2] . "-" . $date_arr[1] . "-" . $date_arr[0];
    return $date;
}

function getEmployeeId($userid) {
    global $dbh;
    $query = "SELECT * FROM user JOIN tblpromotions ON user.staffno = tblpromotions.personnelno WHERE (user.id = ?)";
    $result = $dbh->prepare($query);
    $result->execute(array($userid));
    $employeeid = null;
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $employeeid = $row['employeeid'];
        break;
    }
    return $employeeid;
}

function javascriptTurnedOff() {
    echo "<noscript>";
    echo "<meta HTTP-EQUIV='REFRESH' content='0; url=" . siteUrl("nojavascript.php") . "'>";
    echo "</noscript>";
}

function optionsForFaculties($facultyid) {
    dbConnect();
global $dbh;
    $query = "SELECT * FROM `faculty`";
	$result = $dbh->prepare($query);

    while ($row = $result->fetch(PDO::fetch_ASSOC)) {

        if ($row['facultyid'] == $facultyid) {
            echo "<option selected='selected' value='" . $row['facultyid'] . "'>" . $row['name'] . "</option>";
        } else {
            echo "<option value='" . $row['facultyid'] . "'>" . $row['name'] . "</option>";
        }
    }
}

function get_department_as_array($fids) {
    global $dbh;
    if(is_array($fids)){
    $fids = trim(implode(",", $fids), ", ");
    }
    $output =array();
    $query = "select * from tbldepartment where (facultyid in ?)";
	$result = $dbh->prepare($query);
	$result->execute(array($fids));
    while ($row = $result->fetch(PDO::fetch_ASSOC)) {

        $output[]=array('departmentid'=>$row['departmentid'], 'departmentname'=>$row['name'], 'facultyid'=>$row['facultyid'], 'depttype'=>$row['depttype']);
    }
    return $output;
}

function get_programme_as_array($dids) {
    global $dbh;
    if(is_array($dids)){
    $dids = trim(implode(",", $dids), ", ");
    }
    $output =array();
    $query = "select * from tblprogramme where (departmentid in ?)";
	$result = $dbh->prepare($query);
	$result->execute(array($dids));
    $i = 0;
    while ($row = $result->fetch(PDO::fetch_ASSOC)) {

        $output[]=array('programmeid'=>$row['programmeid'], 'programmename'=>$row['name'], 'departmentid'=>$row['departmentid'], 'programmetypeid'=>$row['programmetypeid']);
    }
    return $output;
}

function get_lga_as_array($sids) {
    global $dbh;
    if(is_array($sids)){
    $sids = trim(implode(",", $sids), ", ");
    }
    $output =array();
    $query = "select * from tbllga where (stateid in ?)";
	$result = $dbh->prepare($query);
	$result->execute(array($sids));
    $i = 0;
     while ($row = $result->fetch(PDO::fetch_ASSOC)) {

        $output[]=array('lgaid'=>$row['lgaid'], 'lganame'=>$row['lganame'], 'stateid'=>$row['stateid']);
    }
    return $output;
}

?>
