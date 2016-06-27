<?php
@session_start();

#   Load the required libraries
require_once( 'include/db.init.php' );
require_once('include/translation.php');


#   Create a connection to the database
if( !isset($_POST['user']) or $_POST['user'] == ''){
    $_SESSION['err'] = "Email address is a required field. Check your email and password and try again.";
    $page = 'index.php';
}
elseif (!isset ($_POST['pass']) or $_POST['pass'] == '') {
    $_SESSION['err'] = "Password is a required field. Check your email and password and try again.";
    $page = 'index.php';
}
else{
    $_SESSION['user'] = $_POST['user'];
    $_SESSION['pass'] = $_POST['pass'];
    unset($_SESSION['user_info']);
    $db = db_connect();
    if( is_string( $db ) ){
        $_SESSION['err'] = $db;
        $page = 'index.php';
    }
    else{
        $page = 'patients.php';
    }
}
header('Location: ' .$page);
?>