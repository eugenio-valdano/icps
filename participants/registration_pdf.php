<?php
// get VID from session
require('access.php');
$VID = $_SESSION['VID'];
// store activity in log
//require('sessioner.php');
//howManyIps();

// load functions
require('../util.php');

// connect to db
$dbinfo = explode("\n", file_get_contents('../loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
#$table_excursions = $dbinfo[15];
$table_late = $dbinfo[19];
$table_total = $dbinfo[21];

//choose table
$table = $table_total;


$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');

// ID is sent via GET method
$ID = $_GET['ID'];
$ID_CHECK = $_GET['IDC'];

// CHECK FOR HACKING
$stringa = "SELECT `ID`,`ID_CHECK` FROM " . $table . " WHERE `ID`=" . $ID . " AND `ID_CHECK`='" . $ID_CHECK . "'";
$result_check = $mysqli->query($stringa);
$entries_check = $result_check->num_rows;
if ($entries_check == 0) {
    header('Location: ' . '../rooms/acher.php');
    exit;
}
$result_check->free();


// select single row, using ID
$stringa = "SELECT * FROM " . $table . " WHERE ID = ".$ID;
$result = $mysqli->query($stringa);
$entries = $result->num_rows;

// should never be more than one result! (because ID must be unique)
if ($entries < 1) {
    die("ID NOT FOUND !");
} elseif ($entries >1) {
    die("CONFLICTING IDs !");
}

// fetch data
$row = $result->fetch_array();

// NOW $row CONTAINS THE DATA
$result->free();
$mysqli->close();



// START PROCESSING
require('fpdf.php');

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        // Logo
        $this->Image('../LOGO.jpg',10,6,30);
        // Arial bold 15
        #$this->SetFont('Arial','B',15);
        // Move to the right
        #$this->Cell(80);
        // Title
        #$this->Cell(30,10,'ICPS TORINO 2017',1,0,'C');
        // Line break
        $this->Ln(50);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,date("Y-m-d") . ' ' . date("H:i:s"),0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(100,10,'ID: ' . $row['ID'] );
$pdf->Ln();
$pdf->Cell(100,10,'SURNAME: ' . $row['SURNAME'] );
$pdf->Ln();
$pdf->Cell(100,10,'NAME: ' . $row['NAME'] );
$pdf->Ln();
$pdf->Ln();

$pdf->Cell(100,10,'EXCURSION: ' . $row['ASSIGNED'] );
$pdf->Ln();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(100,10, '(' . $dexcursions[$row['ASSIGNED']] . ')' );
$pdf->SetFont('Arial','B',14);
$pdf->Ln();
$pdf->Ln();

$pdf->Cell(100,10,'CITY RALLY: ' . $row['CITYRALLY'] );
$pdf->Ln();
$pdf->Cell(100,10,'MAGIC GROUP: ' . $row['MAGO'] );
$pdf->Ln();
$pdf->Ln();

$pdf->Cell(100,10,'RESIDENCE: ' . $row['RESIDENCE'] );
$pdf->Ln();
$pdf->Cell(100,10,'ROOM: ' . $row['ROOM'] );
$pdf->Ln();
$pdf->Ln();

$pdf->Cell(100,10,'WI-FI USERNAME: ' . $row['UNITO_WIFI_USERNAME'] );
$pdf->Ln();
$pdf->Cell(100,10,'WI-FI PASSWORD: ' . $row['UNITO_WIFI_PASSWORD'] );
$pdf->Ln();
$pdf->Output();

?>