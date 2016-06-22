<html>
    <head>
    </head>
    <body>
    <?php
        @session_start();
        header("patients.html");
        echo "Php Version " , phpversion();
    ?>
    </body>
</html>