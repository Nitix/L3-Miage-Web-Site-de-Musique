<?php


//FIXME A faire au propre, en séparant les différentes parties
namespace Views;

class DefaultView
{

    public function DefaultView()
    {

    }

    public function display()
    {
        
    }
} 

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta http-equiv="content-type" content="text/html" charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">


    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Popup en CSS3 et JS pour le blog webdesignweb.fr" />
    <meta name="keywords" content="modal, window, overlay, modern, box, css transition, css animation " />
    <meta name="author" content="Stratis BAKAS" />
    <link rel="shortcut icon" href="../favicon.ico">
    <link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic|Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">

</head>
<body>

    <header>
    <h1> 10H </h1>(lolilol)
    </header>

    <nav>
        
            <input value="" type="text" id="recherche"/>
            <img src="css/icons/search.png " alt="loupe" class="iconBtn" id="btnRecherche"/>
            
            
            
            
            <button onclick="DivInscription()" class="toolbarBtn">Inscription</button>
            <button onclick="DivConnexion()" class="toolbarBtn">Connexion</button>
            <button onclick="profil()" class="toolbarBtn">User</button>
            
            <div class="toolbarSeparator"></div>
            
            <img src="css/icons/gear.png" id="reglages" alt="reglages" class="toolbarBtn iconBtn"/>
            <img src = "css/icons/playlist.png" id ="playlists" alt = "playlists" class="toolbarBtn iconBtn"/> 
            <img src = "css/icons/fav.png" id ="favoris" alt = "favoris" class="toolbarBtn iconBtn"/> 
    </nav>

    <div id ="mainDiv">
        <p>ceci <br>est <br> la <br>div <br>principale </p>
    </div>

    <footer>
        <p>ceci est le pied de page </p>
    </footer>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <script src="js/recherche.js"></script>
    <script src="js/script.js"></script>

</body>
</html>