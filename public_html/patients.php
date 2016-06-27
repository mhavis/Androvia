
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <?php 
        @session_start();
        
        #   Load the required libraries
        require_once('include/translation.php');
        require_once( 'include/db.init.php' );
        
        #   Grab the language dictionary
        $language = translate('English');
        
        
        #   Grab any errors
        $err = '';
        
        if( isset( $_SESSION['err'] ) && ( $_SESSION['err'] != '' ) )
        {
            $err = $_SESSION['err'];
            unset( $_SESSION['err'] );
        }
        
        
        ?>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
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
    </head>
    <body>
    `   <div class="wrapper">

        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <nav class="navbar navbar-fixed-top">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <img src="AndroviaLogo.png" class="img-rounded img-responsive">
                    </div>
                    <div class="col-md-6">
                        <!-- Header Text -->
                    </div>
                </div>
            </div>
        </nav>
        
        <br><br><br><br><br><br>
        

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form method="post" action="controller.php" class="navbar-form form-inline" id="formFindPat">
                        <div class="form-group pull-right">
                            <input type="text" placeholder="<? echo $language['nameF']; ?>" class="form-control" name="NameFirst" id="txtFindPatNameF">
                            <input type="text" placeholder="<? echo $language['nameL']; ?>" class="form-control" name="NameLast" id="txtFindPatNameL">
                            <input type="date" class="form-control" name="DOB" id="dateFindPatDOB">
                            <input type="submit" class="btn btn-primary form-control" value="<? echo $language['find']; ?>" id="btnFindPat" name="findPatient">
                            <input type="submit" class="btn btn-default" value="<? echo $language['new']; ?>" id="btnNewPat" name="newPatient">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <br>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="container">
        <div class="row">
            <div class="col-md-12 displayBox">
            </div>
        </div>
    </div>
    <div class="container">
      <!-- Display a list of the patients -->
      <div class="row">
        <div class="col-md-12" id="divPatList">
            <h1>Patient List</h1>
<!--            <? echo $_SESSION['user'].' '. $_SESSION['pass']; ?>
            <br>
            <? var_dump($_SESSION['user_info']); ?> -->
        </div>
      </div>

      <hr>

      <footer>
        <p>&copy; Company 2015</p>
      </footer>
    </div> <!-- /container -->        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.js"><\/script>')</script>

        <script src="js/vendor/bootstrap.min.js"></script>

        <script src="js/main.js"></script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='//www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X','auto');ga('send','pageview');
        </script>
    </div>
    </body>
</html>
