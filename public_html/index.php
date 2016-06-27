<!--
This is the log in page for the labs uploading images and patient information to the Androvia Server.
All text that is displayed to the user is held in a separate file to make globalization simpler.
-->
<!DOCTYPE php>
<html>
    <head>
        
        <?php 
        @session_destroy();
        @session_start();

        #   Grab any errors
        if( isset( $_SESSION['err'] ) && ( $_SESSION['err'] != '' ) ){
            $err = $_SESSION['err'];
            unset($_SESSION['err']);
        }

        @session_unset();
        
        #   Load the required libraries
        require_once('include/translation.php');
        require_once( 'include/db.init.php' );
        
        #   Grab the language dictionary
        $language = translate('English');
        
        #   Prevent the user from accessing the page with a previous session open
         if( isset( $_POST['user'] ) && ( $_POST['user'] != '' ) ){
            $_SESSION['user'] = $_POST['user'];
  	
	$page = 'controller.php';
        if( isset( $_SESSION['page'] ) && $_SESSION['page'] != '' )
	{
		$page = $_SESSION['page'];
		unset( $_SESSION['page'] );
	}
	header('Location: '.$page);
}
        ?>
        
        
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Log In</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="AndroviaIcon.png">

        <link rel="stylesheet" href="css/bootstrap.min.css">
        <style>
            body {
                padding-top: 50px;
                padding-bottom: 20px;
            }
        </style>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
        <script type="text/javascript" src="js/main.js"></script>
    </head>
    <body>
        <div class="wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form class="form-inline" method="post" action="controller.php" id="formLogin">
                        <div class="form-group">
                            <img src="AndroviaLogo.png">
                            <input type="text" class="form-control" placeholder="<? echo $language['email']?>" name="user" id="txtLoginEmail">
                            <input type="password" class="form-control" placeholder="<? echo $language['pass'] ?>" name="pass" id="txtLoginPass">
                            <input type="submit" class="btn btn-primary" value="<?  echo $language['login'] ?>" id="btnLogin">
                        </div>
                    </form>
                </div>
            </div>
            
            <div class ="container">
                <div class="row">
                    <div class="col-md-12">
                        <h4><? echo $err; ?></h4>
                    </div>
                </div>
            </div>
            <div class="continaer">
                <div class="row">
                    <div class="col-md-12 displayBox">
                    </div>
                </div>
            </div>
        </div>
        </div>
    </body>
</html>