<?php include("includes/variables.php"); ?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>Trabajo DAW</title>
        <meta name="description" content="Trabajo Final de DAW">
        <meta name="author" content="Juanjo">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/jquery-3.3.1.min.js"><\/script>')</script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/funciones.js" type="text/javascript"></script>
        <script src="js/ajaxManager.js" type="text/javascript"></script>
        <script src="js/validador.js" type="text/javascript"></script>
        <script src="js/registro.js" type="text/javascript"></script>
        <script src="js/paginas.js" type="text/javascript"></script>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/gen.css">
        <link rel="stylesheet" href="css/login.css">
        <link rel="stylesheet" href="css/registro.css">
        <link rel="stylesheet" href="css/buscador.css">
        <link rel="stylesheet" href="css/pedidos.css">
        <link rel="stylesheet" href="css/articulos.css">
    </head>
    <body>
        <header>
            <?php include("includes/logueado.php"); ?>
            <?php include("includes/menu.php"); ?>
        </header>
        <main>
            <?php include("includes/pages.php"); ?>
            <?php include("includes/exitos.html"); ?>
            <?php include("includes/errores.html"); ?>
        </main>
        <footer>
            <?php include("includes/footer.php"); ?>
        </footer>
    </body>
</html>
