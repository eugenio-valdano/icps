<?php
// get VID from session
require('../access.php');
$VID = $_SESSION['VID'];
// store activity in log
//require('sessioner.php');
//howManyIps();
//functions
require('../../util.php');

// connect to db
$dbinfo = explode("\n", file_get_contents('../../loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
$table_late = $dbinfo[19];
$table_total = $dbinfo[21];

// table : choose the table
$table = $table_total;

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');


// check if string query is directly given

if ( isset($_POST['STRING_1']) and isset($_POST['STRING_2']) and isset($_POST['STRING_3']) ) {

    $s1 = $_POST['STRING_1'];
    $s2 = ( $_POST['STRING_2']=='' ? '' : ' WHERE '.$_POST['STRING_2'] . ' ' );
    $s3 = ( $_POST['STRING_3']=='' ? '' : ' ORDER BY '.$_POST['STRING_3'] . ' ' );

    $stringa = 'SELECT ' . $s1 . ' FROM ' . $table . $s2 . $s3;

} else {

    // *********** create query string here

    // 1. read POST
    $selected = array();
    $optional = array();
    foreach ($_POST as $key => $val) {

        // selected
        if ($key != 'SORT') {
            $selected[] = '`' . $key . '`';
        }

        // optional
        if ($val == 'opt') {
            $optional[] = $key;
        }
    }

    // 2. check if there are optional variables, and if so, set conditions
    if (count($optional)==0) {
        $conditions = '';
    } else {
        $conditions = [];
        foreach ($optional as $v) {
            $conditions[] = ' ( `' . $v . '` IS NOT NULL AND `' . $v . '`!="" ) ';
        }
        if (count($conditions)>1) {
            $conditions = implode(" OR ", $conditions);
        } else {
            $conditions = $conditions[0];
        }
        $conditions = ' WHERE ' . $conditions;
    }

    // 3. build query string
    $selected = implode(",",$selected); // create comma-sep list of variables as a single string

    $stringa = 'SELECT ' . $selected . ' FROM `' . $table_total . '` ' . $conditions . ' ORDER BY `' . $_POST['SORT'] . '`;'; 

    // *********** end query string
}





$result = $mysqli->query($stringa);
if (!$result) die('Couldn\'t fetch records');
$num_fields = mysqli_num_fields($result);
$headers = array();
while ($fieldinfo = mysqli_fetch_field($result)) {
    $headers[] = $fieldinfo->name;
}
$fp = fopen('php://output', 'w');
if ($fp && $result) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="export.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    fputcsv($fp, $headers);
    while ($row = $result->fetch_array(MYSQLI_NUM)) {
        fputcsv($fp, array_values($row));   
    }
    die;
}
?>