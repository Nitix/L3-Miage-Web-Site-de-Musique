$.widget("custom.catcomplete", $.ui.autocomplete, {
    _create: function () {
        this._super();
        this.widget().menu("option", "items", "> :not(.ui-autocomplete-category)");
    },
    _renderMenu: function (ul, items) {
        var that = this,
            currentCategory = "";
        $.each(items, function (index, item) {
            var li;
            if (item.category != currentCategory) {
                ul.append("<li class='ui-autocomplete-category'>" + item.category + "</li>");
                currentCategory = item.category;
            }
            li = that._renderItemData(ul, item);
            if (item.category) {
                li.attr("aria-label", item.category + " : " + item.label);
            }
        });
    }
});

$("#recherche").catcomplete({
    source: 'index.php?c=base&a=autocomplete'
});


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
                    console.log(data);
                    $("#tracksFound").append('<ul class="trackList" id="trackList"></ul>');
                    for (var i = 0; i < data.musiques.length; i++) {
                        
                        //on echappe tous les ' afin d'eviter des erreurs d'interpretation
                        title_esc = data.musiques[i].title.replace(/'/g, "\\'");
                        name_esc = data.musiques[i].name.replace(/'/g, "\\'");
                        url_esc = data.musiques[i].mp3_url.replace(/'/g, "\\'");
             
                        if (data.musiques[i].title != null) {
                            var playBtn = '<img src="css/icons/play.png " id="playTrack' + data.musiques[i].track_id + '" class="iconBtn" data-id="' + data.musiques[i].track_id + '" onclick="lire(' + data.musiques[i].track_id + ',\'' + title_esc + '\',\'' + name_esc + '\','+data.musiques[i].artist_id+',\''+url_esc+'\')"/>';

                            var addToPlaylistBtn = '<img src="css/icons/addToPlaylist.png " id="addToPlaylist' + data.musiques[i].track_id + '" class="iconBtn" data-id="' + data.musiques[i].track_id + '" onclick="addToPlaylist(' + data.musiques[i].track_id + ',\'' + title_esc + '\',\'' + name_esc + '\','+data.musiques[i].artist_id+',\''+url_esc+'\')"/>';

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

function viewArtistPageFromPlaylist(artist_id)
{
    $("#entetePlaylist").click();
    viewArtistPage(artist_id);
}

function viewArtistPage(artist_id) {
    console.log("page de l'artiste : " + parseInt(artist_id));
    //ouvrir la page de l'artiste
    
    $.ajax({
         url : 'index.php', //url du script PHP qu'on appelle
         type : 'GET', // Le type de la requête HTTP, ici  GET
         data : 'c=base&a=getArtistPage&id='+artist_id,
         dataType : 'JSON', //on demande du JSON en retour
         success: function(data){
              if(voletPlaylistOuvert)
            {
                $("#entetePlaylist").click();
            }
            
            $("#mainDiv").empty();
                        
            $("#mainDiv").append('<div id="artistPageEntete"><img src="'+data.artiste.image_url+'" class="artistImage"/><h1>'+data.artiste.name+'</h1></div><div id="artistInfos"></div>');
            $("#artistInfos").append('<p>'+data.artiste.info+'</p>');
            
            $("#mainDiv").append('<h3>Musiques</h3><ul class="trackList" id="trackList"></ul>');
            
            for (var i = 0; i < data.musiques.length; i++) {
             
                if (data.musiques[i].title != null) {
                    
                    //on echappe les ' pour eviter les erreurs d'interpretation
                    title_esc = data.musiques[i].title.replace(/'/g, "\\'");
                    name_esc = data.artiste.name.replace(/'/g, "\\'");
                    url_esc = data.musiques[i].mp3_url.replace(/'/g, "\\'");
                    
                    var playBtn = '<img src="css/icons/play.png " id="playTrack' + data.musiques[i].track_id + '" class="iconBtn" data-id="' + data.musiques[i].track_id + '" onclick="lire(' + data.musiques[i].track_id + ',\'' + title_esc + '\',\'' + name_esc + '\','+data.musiques[i].artist_id+',\''+url_esc+'\')"/>';

                    var addToPlaylistBtn = '<img src="css/icons/addToPlaylist.png " id="addToPlaylist' + data.musiques[i].track_id + '" class="iconBtn" data-id="' + data.musiques[i].track_id + '" onclick="addToPlaylist(' + data.musiques[i].track_id + ',\'' + title_esc + '\',\'' + name_esc + '\','+data.musiques[i].artist_id+',\''+url_esc+'\')"/>';

                    var favBtn = '<img src="css/icons/fav.png " id="addToFavs' + data.musiques[i].track_id + '" class="iconBtn" data-id="' + data.musiques[i].track_id + '" onclick="addToFavs(' + data.musiques[i].track_id + ')"/>';

                    var artist = "";

                    if (data.musiques[i].name != null) {
                        artist = '<a class="trackList_artist" onclick="viewArtistPage(' + data.musiques[i].artist_id + ')">'+data.musiques[i].name+'</a>';
                    }

                    $("#trackList").append('<li><div class="trackActions">' + favBtn + addToPlaylistBtn + playBtn + '</div><div class="trackInfos">' + data.musiques[i].title + artist + '</div></li>');
                }

            }
         }
    });
}

function lire(track_id, track_title, track_artist, artist_id, mp3_url) {
   
    $("#playerInfos").empty();
    $("#playerInfos").append(track_title+'<a class="trackList_artist" onclick="viewArtistPage('+artist_id+')">'+track_artist+'</a>');
    
    playlistCourante.playingTrack = null;
    surlignerMusiqueEnCours();
    
    p.setTrack(mp3_url);
    p.play();
}

function lireDepuisPlaylist(track_id, track_title, track_artist, artist_id, mp3_url, position){
   
    $("#playerInfos").empty();
    $("#playerInfos").append(track_title+'<a class="trackList_artist" onclick="viewArtistPage('+artist_id+')">'+track_artist+'</a>');
    
    playlistCourante.playingTrack = position;
    surlignerMusiqueEnCours();
   
    p.setTrack(mp3_url);
    p.play();
}

function addToPlaylist(track_id, track_title, track_artist, artist_id, track_url) {
   
    //ouvrir la fenetre des playlists pour en choisir une ou ajouter la musique
    $("#playlistsPopup").remove();
    $("body").append('<div id="playlistsPopup"></div>');

    //calcul de la coordonnees Y de la popup
    var y = currentMousePos.y;
    
    if (y + $("#playlistsPopup").height() > ($("html").height() - 120)) {
        y = $("html").height() - $("#playlistsPopup").height() - 120 - 10;
    }

    $("#playlistsPopup").offset({
        top: y,
        left: (currentMousePos.x - $("#playlistsPopup").width() - 20)
    });
    $("#playlistsPopup").append('<img src="css/icons/close.png" class="iconBtn" onclick="$(\'#playlistsPopup\').remove()"/>');
    $("#playlistsPopup").append('<div id="enteteAddToPlaylist">Ajouter ' + track_title + ' - ' + track_artist + ' à :</div>');
    $("#playlistsPopup").append('<ul id="userPlaylists" class="trackList"></ul>');

    //le premier LI sert a ajouter une playlist si besoin
    $("#userPlaylists").empty();
    $("#userPlaylists").append('<li id="createPlaylistLi" data-edition-mode="false"><img src="css/icons/add.png" class="iconBtn"/><p>Nouvelle playlist...<p></li>');
    
    //on recupere les playlist dans la SESSION
    getPlaylistsInSession(track_id, track_title, track_artist, artist_id, track_url);
   

    //si on clique dessus, un formulaire pour entrer le nom de la playlist a créer s'ouvre dans le li
    $("#createPlaylistLi").click(function(){
        addNewPlaylistInPopup(track_id, track_title, track_artist, artist_id, track_url);
    });
    
     
}

function getPlaylistsInSession(track_id, track_title, track_artist, artist_id, track_url)
{
    console.log("TRACK "+track_id);
    $.ajax({
         url : 'index.php', //url du script PHP qu'on appelle
         type : 'GET', // Le type de la requête HTTP, ici  GET
         data : 'c=playlist&a=getPlaylists',
         dataType : 'JSON', //on demande du JSON en retour
         success: function(data){

             if(data.length == 0)
             {
                 //aucune playlist
                 $("#userPlaylists").append("Aucune playlist");
                 
             }
             else
             {
                 //afficher les playlists
                console.log(data);
                 
                 for(var i=0; i < data.length ; i++)
                 {
                     //on echappe les ' pour eviter les erreurs d'interpretation
                     playname_esc = data[i].playlist_name.replace(/'/g, "\\'");
                    title_esc = track_title.replace(/'/g, "\\'");
                    name_esc = track_artist.replace(/'/g, "\\'");
                    url_esc = track_url.replace(/'/g, "\\'");
                    
                     $("#userPlaylists").append('<li class="playLi" id="playLiId'+data[i].playlist_id+'" data-name="'+playname_esc+'"><p onclick="addToThisPlaylist('+track_id+',\''+ title_esc+'\',\''+ name_esc+'\','+ artist_id+',\''+ url_esc+'\','+data[i].playlist_id+')">'+data[i].playlist_name+'</p><img src="css/icons/bin.png" class="iconBtn" onclick="delPlaylistInPopup('+track_id+',\''+ title_esc+'\',\''+ name_esc+'\','+ artist_id+',\''+ url_esc+'\','+data[i].playlist_id+', \'playLiId'+data[i].playlist_id+'\')"/></li>');
                 }
                 
             }
         }
     });
}

function addToThisPlaylist(track_id, track_title, track_artist, artist_id, track_url, playlist_id)
{
    $.ajax({
         url : 'index.php', //url du script PHP qu'on appelle
         type : 'GET', // Le type de la requête HTTP, ici  GET
         data : 'c=playlist&a=addTrackToPlaylist&trid='+track_id+'&trtitle='+track_title+'&trart='+track_artist+'&artid='+artist_id+'&trurl='+track_url+'&plid='+playlist_id,
         dataType : 'JSON', //on demande du JSON en retour
         success: function(data){

             if(data == true)
             {
                 //si on vient de modifier une playlist actuellement en cours de lecture, on la met a jour dans le volet
                 if($("#playlistInfos").attr("data-playlist-id") == playlist_id)
                 {
                     console.log("charger");
                     chargerPlaylist(playlist_id, false);
                 }
                 
                 //on ordonne a la popup de se fermer automatiquement au bout de 2.5sec
                 var callbackId = setTimeout(function(){
                     $('#playlistsPopup').remove()
                 },2500);
                 
                $('#playlistsPopup').empty();
                $("#playlistsPopup").append('<img src="css/icons/close.png" id="popupCloseBtn" class="iconBtn"/>');
                
                $("#playlistsPopup").children('#popupCloseBtn').click(function(){
                    //si l'utilisateur clique sur le bouton CLOSE de la popup, on annule l'evenement de fermeture auto au bout de 2.5sec
                    clearTimeout(callbackId);
                    $('#playlistsPopup').remove();
                });
                
                $('#playlistsPopup').append("<p>La musique -"+track_title+"- a bien été ajoutée à cette playlist</p>");
                $('#playlistsPopup').children("p").css("color","#B1FF00");
                $('#playlistsPopup').height(80);
                 //$("#playLiId"+playlist_id).notify("La musique -"+track_title+"- a bien été ajoutée à cette playlist","success");
                 //alert("La musique -"+track_title+"- a bien été ajoutée à cette playlist");
                 
                 
                 
             }
         }
     });
}

function addToFavs(track_id) {
    console.log("ajout aux favs de : " + track_id);
    //ajouter la musique aux favs
}

function delPlaylistInPopup(track_id, track_title, track_artist, artist_id, track_url, playlist_id, elementId){
    
     if($("#playlistInfos").attr("data-playlist-id") == playlist_id)
    {
        $("#"+elementId).notify("impossible de supprimer une playlist en cours de lecture !");
    }
    else
    {
        $.ajax({
             url : 'index.php', //url du script PHP qu'on appelle
             type : 'GET', // Le type de la requête HTTP, ici  GET
             data : 'c=playlist&a=delPlaylist&id='+playlist_id,
             dataType : 'JSON', //on demande du JSON en retour
             success: function(data){
                 
                 if(data == true)
                 {
                     var tmp = $("#createPlaylistLi");
                    $("#userPlaylists").empty();
                    $("#userPlaylists").append(tmp);
                                    
                    //on met a jour la liste des playlists
                    getPlaylistsInSession(track_id, track_title, track_artist, artist_id, track_url);
                    
                    //si on clique dessus, un formulaire pour entrer le nom de la playlist a créer s'ouvre dans le li
                    $("#createPlaylistLi").click(function(){
                        addNewPlaylistInPopup(track_id, track_title, track_artist, artist_id, track_url);
                    });
                 }
             }
         });
    }
}

function addNewPlaylistInPopup(track_id, track_title, track_artist, artist_id, track_url) {
    //si on n'est pas en mode edition, on y entre
    $("#createPlaylistLi").animate({
        backgroundColor: "#2E2E2E"
    }, 1);
    
    
    if ($("#createPlaylistLi").attr("data-edition-mode") == "false") {
        
        $("#createPlaylistLi").empty();
        $("#createPlaylistLi").attr("data-edition-mode", true);
        $("#createPlaylistLi").append('<img src="css/icons/add.png" id="submitNewPlaylistBtn" class="iconBtn"/><input id="newPlaylistName" type="text" value="Nom de la playlist...">');
        $("#newPlaylistName").focus();
        var noNameYet = true;

        $("#newPlaylistName").keydown(function () {
            if (noNameYet) {
                
                noNameYet = false;
                console.log(noNameYet);
                $("#newPlaylistName").val("");
            }

        });

        $("#submitNewPlaylistBtn").click(function () {
            var playlistName = $("#newPlaylistName").val();

            //verifier si le nom n'existe pas deja en SESSION
            alreadyExists = false;
            
            //boucle qui verifie s'il n'existe pas deja une playlist de ce nom
            $("#userPlaylists").children().each(function(){
                if($(this).attr("data-name") == playlistName)
                {
                    alreadyExists = true;
                }
            });

            if (!noNameYet) {
                if (alreadyExists) {
                    //dans ce cas, le nom existe deja
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
                    setTimeout(function () {
                        $("#createPlaylistLi").click(addNewPlaylistInPopup);
                    }, 1);
                } else {

                    //ajout en base puis en SESSION, rafraichissement de la liste dans la popup pour afficher la nouvelle playlist, et retour a la normale pour createPlaylistLi
                    $.ajax({
                         url : 'index.php', //url du script PHP qu'on appelle
                         type : 'GET', // Le type de la requête HTTP, ici  GET
                         data : 'c=playlist&a=addPlaylist&newPlaylistName='+playlistName,
                         dataType : 'JSON', //on demande du JSON en retour
                         success: function(data){
                            if(data != false)
                            {
                                
                                //on remet le bouton d'ajout de playlist a l'etat normal
                                $("#createPlaylistLi").off("click");
                                
                                $("#createPlaylistLi").attr("data-edition-mode", false);
                                $("#createPlaylistLi").children("p").remove();
                                $("#createPlaylistLi").children("input").remove();
                                $("#createPlaylistLi").append("<p>Nouvelle playlist...<p>");
                                
                                noNameYet = true;
                                
                                var tmp = $("#createPlaylistLi");
                                $("#userPlaylists").empty();
                                $("#userPlaylists").append(tmp);
                                
                                //on met a jour la liste des playlists
                                getPlaylistsInSession(track_id, track_title, track_artist, artist_id, track_url);
                                
                                //on laisse un intervale de 1 ms pour ajouter a niveau le handler, afin d'eviter un bouclage d'execution de ce handler
                                setTimeout(function () {
                                    
                                    //si on clique dessus, un formulaire pour entrer le nom de la playlist a créer s'ouvre dans le li
                                    $("#createPlaylistLi").click(function(){
                                        addNewPlaylistInPopup(track_id, track_title, track_artist, artist_id, track_url);
                                    });
                                }, 1);
                            }
                         }
                     });

                    

                }
            }
        });

        $("#newPlaylistName").keydown(function (event) {
            if (event.keyCode == 13) {
                $("#submitNewPlaylistBtn").click();
            }
        });
    }

}