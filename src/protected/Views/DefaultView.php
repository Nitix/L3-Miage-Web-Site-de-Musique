<?php


//FIXME A faire au propre, en séparant les différentes parties
namespace Views;

use Models\User;

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
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/style.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Popup en CSS3 et JS pour le blog webdesignweb.fr" />
    <meta name="keywords" content="modal, window, overlay, modern, box, css transition, css animation " />
    <link rel="shortcut icon" href="favicon.ico">
    <!--<link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic|Montserrat:400,700' rel='stylesheet' type='text/css'>-->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<title>10H - Site de musique</title>
</head>
<body>
    <header>
    <h1> 10H </h1>Un max de son pour bien commencer la journée
    </header>

    <nav>

            <input value="" type="text" id="recherche"/>
            <img src="css/icons/search.png " alt="loupe" class="iconBtn" id="btnRecherche"/>


            <?php if (User::getCurrentUser()->isVisitor()) : ?>
            <button id="buttonInscription" onclick="DivInscription()" class="toolbarBtn">Inscription</button>
            <button id="buttonConnexion" onclick="DivConnexion()" class="toolbarBtn">Connexion</button>
            <?php endif; ?>
            <button id="usernameProfile" onclick="profil()" class="toolbarBtn"><?php  echo "Bienvenue "; if (User::getCurrentUser()->isVisitor()) echo "Visiteur"; else  echo User::getCurrentUser()->getUsername(); ?></button>
            <button id="buttonDeconnexion" onclick="disconnect()" class="toolbarBtn" <?php if(User::getCurrentUser()->isVisitor()) echo "hidden"; ?>>Deconnexion</button>

            <div class="toolbarSeparator"></div>

            <img src="css/icons/gear.png" id="reglages" alt="reglages" class="toolbarBtn iconBtn"/>
            <img src = "css/icons/playlist.png" id ="playlists" alt = "playlists" class="toolbarBtn iconBtn" onclick="afficherPlaylists()"/>
            <img src = "css/icons/fav.png" id ="favoris" alt = "favoris" class="toolbarBtn iconBtn"/>
    </nav>




    <div id ="mainDiv">
        <p>Bienvenue sur 10h, utilisez la barre de recherche en haut à gauche en lancez-vous !</p>
        <p>Site web réalisé par :  </p>
        <ul>
            <li>Papelier Romain</li>
            <li>Pierson Guillaume</li>
            <li>Verhoof Tom</li>
            <li>Ahmed-Khalifa Aminetou</li>
        </ul>
    </div>



    <footer>
        <div id="entetePlaylist"><img alt="" src="css/icons/voletUp.png" data-src-up="css/icons/voletUp.png" data-src-down="css/icons/voletDown.png" class="iconBtn" id="entetePlaylistBtn">
        <div id="playlistInfos"></div>
        </div>
        <div id="voletPlaylist">
            <ul class="trackList">

            </ul>
        </div>
        <div id="player">
            <div id="playerUI">
                <audio id="audio"><source src="#" type="audio/mpeg"></audio>
                <img id="previousTrack" alt="Précédent" class="iconBtn" src="css/icons/previous.png" />
                <img id="play" alt="Play" class="iconBtn" src="css/icons/play.png" />
                <img id="nextTrack" alt="Suivant" class="iconBtn" src="css/icons/next.png" />

                <progress id="musicProgress" value="0" max="1"></progress>
                <!-- FIXME Screenreader - label -->
                <input id="cursorMusic" min="0"  max="100" step="1" value="0" type="range" />
                <span id="volumeGroup">
                    <img id="volumeMute" alt="Muet" class="iconBtn" src="css/icons/volume.png" />
                    <span id="volumeControlContainer" style="display:none">
                        <input id="volume" min="0" max="100" step="1" value="100" type="range"  />
                    </span>
                </span>
            </div>
            <div id="playerInfos">

            </div>
        </div>
    </footer>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <script src="js/notify.min.js"></script>

    <script src="js/recherche.js"></script>
    <script src="js/script.js"></script>

    <script src="js/Playlist.js"></script>
    <script src="js/Player.js"></script>


</body>
</html>