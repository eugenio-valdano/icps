<?php
function getIP() {
    $ip="";
    if (getenv("HTTP_CLIENT_IP")) $ip = getenv("HTTP_CLIENT_IP");
    else if(getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if(getenv("REMOTE_ADDR")) $ip = getenv("REMOTE_ADDR");
    else $ip = "";
    return $ip;
}

function howManyIps() {
    $filename = "./connected.log";
    $seconds = 300;
    $yourIP = getIP();

    if (file_exists($filename.".lock")) $readonly = true; else $readonly=false;

    //lock the file
    if (!$readonly) $fpLock = fopen($filename . ".lock", "w");

    //read data ips
    if (file_exists($filename)) {

        //$fp = fopen($filename, "r");
        $arIPS = explode("\n", file_get_contents($filename));
        //$arIPS=explode ("\n", fread($fp,filesize($filename)) );
        //fclose($fp);

        //update data and search user ip
        $s = "";
        $already=false;

        foreach($arIPS as $row) {

            // check if line is not empty
            $arData= explode (" ", $row);

            // skip empty lines
            if (count($arData)<2) {
                continue;
            }

            //update your user timer
            if (!$already and $yourIP==$arData[0]) {
                $already=true;
                $arData[1]=time();
            }
            $s.=$arData[0]." ".$arData[1]."\n";


        } // end foreach

        if (!$already) {
            //your user is new, add it to the list
            $s.=$yourIP." ".time()."\n";
        }

    }  else {// end if file exists filename
        $s=$yourIP." ".time()."\n";
    }

    //save the list
    $fp = fopen($filename, "w");
    fwrite($fp,$s);
    fclose($fp);

    //remove thr lock
    fclose($fpLock);
    unlink($filename.".lock");

}
?>