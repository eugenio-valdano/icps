<?php
session_start();

session_unset();
session_destroy();

require('access.php');
header('Location: ' . 'index.php');
?>