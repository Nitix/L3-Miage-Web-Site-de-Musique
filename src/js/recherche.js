$('#recherche').autocomplete({
    source : 'index.php?c=base&a=autocomplete'
});

/*
$.ajax({
    url: 'index.php?c=base&a=recherche',
    success: function(data){
        console.log(data);
    }
});
*/

//ajout de l'evenement pour lancer la recherche avec la touche Entree
 $("#recherche").keydown(function(event){
     if(event.keyCode == 13){
         $("#btnRecherche").click();
     }
 });

//ajout de l'évenement "quand on clique sur le bouton de recherche"
$("#btnRecherche").click(function() {

    //appel ajax vers le script php
    $.ajax({
        url: 'index.php', //url du script PHP qu'on appelle
        type: 'GET', // Le type de la requête HTTP, ici  GET
        data: 'c=base&a=recherche&q=' + $("#recherche").val(), // c = controlleur PHP a executer, a = methode de ce controlleur a executer, q = recherche
        dataType: 'JSON', //on demande du JSON en retour
        success: function(data) {
            console.log(data);
            //ici on va construire la liste des resultats de la recherche

            $("#mainDiv").empty();


            if ((data.musiques.length == 0) && (data.artistes.length == 0)) {
                $("#mainDiv").append('<br><h3>Aucun artiste un morceau trouvé</h3>');
            } else {
                $("#mainDiv").append('<div id="tracksFound"></div>');
                $("#tracksFound").append('<h3>Musiques trouvées</h3>');

                if (data.musiques.length == 0) {
                    $("#tracksFound").append('<h5>Aucune musique trouvée</h5>');
                } else {
                    $("#tracksFound").append('<ul class="trackList" id="trackList"></ul>');
                    for (var i = 0; i < data.musiques.length; i++) {
                        if (data.musiques[i].title != null) {
                            var playBtn = '<img src="css/icons/play.png " id="playTrack' + data.musiques[i].track_id + '" class="iconBtn" data-id="' + data.musiques[i].track_id + '" onclick="lire(' + data.musiques[i].track_id + ')"/>';

                            var addToPlaylistBtn = '<img src="css/icons/addToPlaylist.png " id="addToPlaylist' + data.musiques[i].track_id + '" class="iconBtn" data-id="' + data.musiques[i].track_id + '" onclick="addToPlaylist(' + data.musiques[i].track_id + ',\'' + data.musiques[i].title + '\',\'' + data.musiques[i].name + '\')"/>';

                            var favBtn = '<img src="css/icons/fav.png " id="addToFavs' + data.musiques[i].track_id + '" class="iconBtn" data-id="' + data.musiques[i].track_id + '" onclick="addToFavs(' + data.musiques[i].track_id + ')"/>';

                            var artist = "";

                            if (data.musiques[i].name != null) {
                                artist = '<a class="trackList_artist" onclick="viewArtistPage(' + data.musiques[i].artist_id + ')">' + data.musiques[i].name + '</a>';
                            }

                            //$("#trackList").append('<li><div class="trackInfos">'+data.musiques[i].title+artist+'</div><div class="trackActions">'+favBtn+addToPlaylistBtn+playBtn+'</div></li>');
                            $("#trackList").append('<li><div class="trackActions">' + favBtn + addToPlaylistBtn + playBtn + '</div><div class="trackInfos">' + data.musiques[i].title + artist + '</div></li>');
                        }

                    }
                }

                $("#mainDiv").append('<div id="artistsFound"></div>');
                $("#artistsFound").append('<h3>Artistes trouvés</h3>');

                if (data.artistes.length == 0) {
                    $("#artistsFound").append('<h5>Aucun artiste trouvé</h5>');
                } else {
                    $("#artistsFound").append('<ul class="artistsList" id="artistsList"></ul>');
                    for (var i = 0; i < data.artistes.length; i++) {
                        if (data.artistes[i].name != null)
                            $("#artistsList").append('<li onclick="viewArtistPage(' + data.artistes[i].artist_id + ')"><img src="' + data.artistes[i].image_url + '"/><div class="artistsList_name">' + data.artistes[i].name + '</div></li>');
                    }
                }
            }
        }
    });


});

function viewArtistPage(artist_id) {
    console.log("page de l'artiste : " + artist_id);
    //ouvrir la page de l'artiste
}

function lire(track_id) {
    console.log("lecture : " + track_id);
    //balancer la musique dans le lecteur
}

function addToPlaylist(track_id, track_title, track_artist) {
    console.log("ajout a une playlist de : " + track_id);
    //ouvrir la fenetre des playlists pour en choisir une ou ajouter la musique
    $("#playlistsPopup").remove();
    $("body").append('<div id="playlistsPopup"></div>');

    //calcul de la coordonnees Y de la popup
    var y = currentMousePos.y;

    if (y + $("#playlistsPopup").height() > ($("html").height() - $("footer").height())) {
        y = $("html").height() - $("#playlistsPopup").height() - $("footer").height() - 10;
    }

    $("#playlistsPopup").offset({
        top: y,
        left: (currentMousePos.x - $("#playlistsPopup").width() - 20)
    });
    $("#playlistsPopup").append('<img src="css/icons/close.png" class="iconBtn" onclick="$(\'#playlistsPopup\').remove()"/>');
    $("#playlistsPopup").append('<div id="enteteAddToPlaylist">Ajouter ' + track_title + ' - ' + track_artist + ' à :</div>');
    $("#playlistsPopup").append('<ul id="userPlaylists" class="trackList"></ul>');

    //le premier LI sert a ajouter une playlist si besoin
    $("#userPlaylists").append('<li id="createPlaylistLi" data-edition-mode="false"><img src="css/icons/add.png" class="iconBtn"/><p>Nouvelle playlist...<p></li>')

    for (var i = 0; i < 20; i++) {
        $("#userPlaylists").append('<li>aaaaaaaaaaaaaa</li>');
    }

    //si on clique dessus, un formulaire pour entrer le nom de la playlist a créer s'ouvre dans le li
    $("#createPlaylistLi").click(addNewPlaylistInPopup);
    /*
     $.ajax({
     url : 'index.php', //url du script PHP qu'on appelle
     type : 'GET', // Le type de la requête HTTP, ici  GET
     data : 'c=base&a=getUserPlaylists',
     dataType : 'JSON', //on demande du JSON en retour
     success: function(data){
     }
     });*/
}

function addToFavs(track_id) {
    console.log("ajout aux favs de : " + track_id);
    //ajouter la musique aux favs
}
function addNewPlaylistInPopup() {
    //si on n'est pas en mode edition, on y entre
    $("#createPlaylistLi").animate({
        backgroundColor: "#2E2E2E"
    }, 1);

    if ($(this).attr("data-edition-mode") == "false") {
        console.log($(this).attr("data-edition-mode"));
        $(this).empty();
        $(this).attr("data-edition-mode", true);
        $(this).append('<img src="css/icons/add.png" id="submitNewPlaylistBtn" class="iconBtn"/><input id="newPlaylistName" type="text" value="Nom de la playlist...">');
        $("#newPlaylistName").focus();
        var noNameYet = true;

        $("#newPlaylistName").keydown(function() {
            if (noNameYet) {
                noNameYet = false;
                $(this).val("");
            }

        });

        $("#submitNewPlaylistBtn").click(function() {
            var playlistName = $("#newPlaylistName").val();

            //verifier si le nom n'existe pas deja en base
            //si oui,
            alreadyExists = true;

            if (!noNameYet) {
                if (alreadyExists) {
                    //on affiche un message d'erreur sur background color rouge orange, et on quitte le mode edition

                    $("#createPlaylistLi").off("click");
                    $("#createPlaylistLi").empty();
                    $("#createPlaylistLi").append("<p>Cette playlist existe déjà !<p>");
                    $("#createPlaylistLi").attr("data-edition-mode", false);
                    noNameYet = true;
                    $("#createPlaylistLi").animate({
                        backgroundColor: "#DE3F2A"
                    }, 1);

                    //on laisse un intervale de 1 ms pour ajouter a niveau le handler, afin d'eviter un bouclage d'execution de ce handler
                    setTimeout(function() {
                        $("#createPlaylistLi").click(addNewPlaylistInPopup);
                    }, 1);
                } else {

                    //ajout en base, rafraichissement de la liste dans la popup pour afficher la nouvelle playlist, et retour a la normale pour createPlaylistLi

                    $("#createPlaylistLi").off("click");
                    $("#createPlaylistLi").empty();
                    $("#createPlaylistLi").attr("data-edition-mode", false);
                    $("#createPlaylistLi").append("<p>Nouvelle playlist...<p>");
                    noNameYet = true;
                    //on laisse un intervale de 1 ms pour ajouter a niveau le handler, afin d'eviter un bouclage d'execution de ce handler
                    setTimeout(function() {
                        $("#createPlaylistLi").click(addNewPlaylistInPopup);
                    }, 1);

                }
            }
        });

        $("#newPlaylistName").keydown(function(event) {
            if (event.keyCode == 13) {
                $("#submitNewPlaylistBtn").click();
            }
        });
    }

}