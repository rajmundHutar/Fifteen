<!doctype html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="author" content="rajmund.cz">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <title>Patnáctka</title>
    </head>
    <body>
        <?php
        require_once "./class/Patnactka.php";

        $game = new Patnactka();

        if (isset($_GET["admin"])) {
            $game->renderAdmin();
        } else if (isset($_GET["pic"])) {
            $setup = array(
                "pic" => $_GET["pic"],
            );
            $game->setupGame($setup);
            $game->renderGame();
        } else {
            $game->renderMenu();
        }
        ?>
    </body>
</html>