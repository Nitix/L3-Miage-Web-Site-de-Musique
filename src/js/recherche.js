$.widget( "custom.catcomplete", $.ui.autocomplete, {
    _create: function() {
        this._super();
        this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
    },
    _renderMenu: function( ul, items ) {
        var that = this,
            currentCategory = "";
        $.each( items, function( index, item ) {
            var li;
            if ( item.category != currentCategory ) {
                ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
                currentCategory = item.category;
            }
            li = that._renderItemData( ul, item );
            if ( item.category ) {
                li.attr( "aria-label", item.category + " : " + item.label );
            }
        });
    }
});

$( "#recherche" ).catcomplete({
    source: 'index.php?c=base&a=autocomplete'
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
$("#recherche").keydown(function (event) {
    if (event.keyCode == 13) {
        $("#btnRecherche").click();
    }
});

//ajout de l'évenement "quand on clique sur le bouton de recherche"
$("#btnRecherche").click(function () {

    //appel ajax vers le script php
    $.ajax({
        url: 'index.php', //url du script PHP qu'on appelle
        type: 'GET', // Le type de la requête HTTP, ici  GET
        data: 'c=base&a=recherche&q=' + $("#recherche").val(), // c = controlleur PHP a executer, a = methode de ce controlleur a executer, q = recherche
        dataType: 'JSON', //on demande du JSON en retour
        success: function (data) {
            console.log(data);
            //ici on va construire la liste des resultats de la recherche

            $("#mainDiv").empty();


            if ((data.musiques.length == 0) && (data.artistes.length == 0)) {
                $("#mainDiv").append('<br><h3>Aucun artiste un morceau trouvé</h3>');
            }
            else {
                $("#mainDiv").append('<div id="tracksFound"></div>');
                $("#tracksFound").append('<h3>Musiques trouvées</h3>');

                if (data.musiques.length == 0) {
                    $("#tracksFound").append('<h5>Aucune musique trouvée</h5>');
                }
                else {
                    $("#tracksFound").append('<ul class="trackList" id="trackList"></ul>');
                    for (var i = 0; i < data.musiques.length; i++) {
                        if (data.musiques[i].title != null) {
                            var playBtn = '<img src="css/icons/play.png " id="playTrack' + data.musiques[i].track_id + '" class="iconBtn" data-id="' + data.musiques[i].track_id + '" onclick="lire(' + data.musiques[i].track_id + ')"/>';

                            var addToPlaylistBtn = '<img src="css/icons/addToPlaylist.png " id="addToPlaylist' + data.musiques[i].track_id + '" class="iconBtn" data-id="' + data.musiques[i].track_id + '" onclick="addToPlaylist(' + data.musiques[i].track_id + ')"/>';

                            var favBtn = '<img src="css/icons/fav.png " id="addToFavs' + data.musiques[i].track_id + '" class="iconBtn" data-id="' + data.musiques[i].track_id + '" onclick="addToFavs(' + data.musiques[i].track_id + ')"/>';

                            var artist = "";

                            if (data.musiques[i].name != null) {
                                artist = '<a class="trackList_artist" onclick="viewArtistPage(' + data.musiques[i].artist_id + ')">' + data.musiques[i].name + '</a>';
                            }

                            $("#trackList").append('<li>' + data.musiques[i].title + artist + favBtn + addToPlaylistBtn + playBtn + '</li>');
                        }

                    }
                }

                $("#mainDiv").append('<div id="artistsFound"></div>');
                $("#artistsFound").append('<h3>Artistes trouvés</h3>');

                if (data.artistes.length == 0) {
                    $("#artistsFound").append('<h5>Aucun artiste trouvé</h5>');
                }
                else {
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

function addToPlaylist(track_id) {
    console.log("ajout a une playlist de : " + track_id);
    //ouvrir la fenetre des playlists pour en choisir une ou ajouter la musique
}

function addToFavs(track_id) {
    console.log("ajout aux favs de : " + track_id);
    //ajouter la musique aux favs
}

