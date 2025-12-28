<?php
$app['default']['root_folder'] = "";

if(!isset($dbh) || !($dbh instanceof PDO)) {
    $dbh = null;

}
if(!function_exists("openConnection")) {
    function openConnection($persistent=false)
    {
        global $dbh;

        if ($dbh != null) {
            return $dbh;
        }
   
        $hostname_cnpgdataport = "localhost";
        $database_cnpgdataport = "dlc_cbt";
        $username_cnpgdataport = "admin";
        $password_cnpgdataport = "lTetDsLeDOCrE";

        try {
            if ($persistent) {
                $dbh = new PDO("mysql:host=$hostname_cnpgdataport;dbname=$database_cnpgdataport", $username_cnpgdataport, $password_cnpgdataport, array(PDO::ATTR_PERSISTENT => true));
            } else {
                $dbh = new PDO("mysql:host=$hostname_cnpgdataport;dbname=$database_cnpgdataport", $username_cnpgdataport, $password_cnpgdataport);
            }
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbh;
        } catch (PDOException $e) {
            $r = siteUrl("site_error.php?errcode=-1");
            header("Location:" . $r);
            exit();
        }
    }
}

    if(!function_exists("siteUrl")){
        function siteUrl($scriptFile="") {
            global $app;

            //$baseUrl = "https://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'];
            $baseUrl = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'];

            return $baseUrl . "/" . $app['default']['root_folder'] . "cbtconverted/" . $scriptFile;
        }
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

function redirectTo($url) {
    header("location: " . $url);
    exit();
}

function redirect($url) {
    redirectTo($url);
}

function clean($str) {
    $str = @trim($str);
    if (get_magic_quotes_gpc()) {
        $str = stripslashes($str);
    }
    return $str;
}

function siteUrl($scriptFile) {
    global $app;
    
    $baseUrl = "http://" . $_SERVER['SERVER_NAME'];
    //$baseUrl = "https://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'];
    
     if ($_SERVER['SERVER_NAME'] != $app['default']['root_folder']){

      return $baseUrl . "/" . $app['default']['root_folder'] . "/" . $scriptFile;
      
       }else{


    return $baseUrl . "/" . $scriptFile;
}
}

function pageTitle($title) {
    return $title . " TestLEAD CBT";
}

function absolutePath($path) {
    return realpath(dirname(__FILE__) . $path);
}

function sanitizeInput($str) {
    $str = @trim($str);
    if (get_magic_quotes_gpc()) {
        $str = stripslashes($str);
    }
    return mysql_real_escape_string($str);
}

function daydiff($from, $to) {
    $diff = strtotime($from) - strtotime($to) + 1; //Find the number of seconds
    $day_difference = ceil($diff / (60 * 60 * 24));  //Find how many days that is
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

?>
