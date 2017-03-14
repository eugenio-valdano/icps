<?php
session_start();

session_unset();
session_destroy();

require('access2.php');
header('Location: ' . 'lcnc.php');
?>