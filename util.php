<?php



function checkp($x,$vid) { // $x is the highest number with clearance. if x=1, then for 0 and 1 it's true

    // 0: full access. 1: selectioners, when active. 2: read only. 3: limited access (IAPS)
    $privileges = array('eugenio'=>0,
                        'michele'=>0,
                        'lorenzo'=>1, // set to 1 when you want them to be able to edit statuses
                        'lucio'=>2,
                        'francesco'=>2,
                        'oc_member'=>2,
                        'iaps'=>3
                       );


    return (isset($privileges) and $privileges[$vid] <= $x);
}


// choose table
function choose_table($ID) {
    
    // connect to db
    $dbinfo = explode("\n", file_get_contents('loginDB.txt'))[0];
    $dbinfo = explode(" ", $dbinfo);
    $table = $dbinfo[11];
    $table_late = $dbinfo[19];
    
    return ( (int)$ID > 500 ? $table_late : $table );
}


// functions
function do_hlink($s,$w) {
    if ($s=='') {
        return $w;
    } else {
        return '<a href="' . $s . '">' . $w . '</a>';
    }
}

function format_contribution($x) {
    if ($x['CONTRIBUTION']=='talk') {
        return do_hlink($x['URL_TALK'],'talk');
    } elseif ($x['CONTRIBUTION']=='post') {
        return do_hlink($x['URL_POSTER'],'poster');
    } elseif ($x['CONTRIBUTION']=='both') {
        return do_hlink($x['URL_TALK'],'talk') . ' & ' . do_hlink($x['URL_POSTER'],'poster');
    } else {
        return "";
    }
}

// function
function status_to_color($s) {
    if ($s == 'waiting') {
        return 'warning';
    } elseif ($s == 'accepted') {
        return 'info';
    } elseif ($s == 'rejected') {
        return 'rejected';
    } elseif ($s == 'participant') {
        return 'participant';
    } elseif ($s == 'withdrawn') {
        return 'danger';
    } elseif ($s == 'proven') {
        return 'success';
    } elseif ($s == 'late') {
        return 'late';
    } else {
        die('UNKNOWN STATUS');
    }
}

// maps status onto bin status
function status_binary($s) {
    if ($s=='participant' or $s=='proven' or $s=='accepted') {
        return true;
    } else {
        return false;
    }
}

// excursions
$dexcursions = array(
    "A" => "National Centre of Oncology Hadrotherapy - Wine tasting",
    "B" => "Energy Research Centre - Monza park and race track",
    "C" => "Italian Institute of Technology - Relax on the beach",
    "D" => "National Institute for Metrological Research - Water fun",
    "E" => "Astrophysical Observatory - Superga and the hill of Torino",
    "F" => "Conservation and Restoration Centre - Venaria Reale",
    "G" => "Modane Underground Laboratory - Mountain trekking",
    "H" => "TotemEnergy – Sacra di San Michele",
    "-" => "<i>not assigned</i>"
);


function excursion_colorer($n) {
    if ($n<50) {
        return '#4ca64c';
    } elseif ($n==50) {
        return '#ffe34c';
    } else {
        return '#ff7f7f';
    }
}


// function for capping
function fcapping($x,$s) {

    if ($s=='IM') {
        $kapping = 900;
        $uarning = 900;
    } elseif ($s=='NC Italy') {
        $kapping = 20;
        $uarning = 18;
    } else {
        $kapping = 30;
        $uarning = 28;
    }

    if ($x>$kapping) {
        return "style=\"background-color:red;color:white\"";
    } elseif ($x>$uarning) {
        return "style=\"background-color:#e5c100;color:white\"";
    } else {
        return "style=\"background-color:green;color:white\"";   
    }

}

function fcapping_tot($x) {

    if ($x>250) {
        return "style=\"background-color:red;color:white\"";
    } elseif ($x>240) {
        return "style=\"background-color:#e5c100;color:white\"";
    } else {
        return "style=\"background-color:green;color:white\"";   
    }

}



?>


