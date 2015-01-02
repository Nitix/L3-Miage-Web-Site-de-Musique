var footerHeight = $("footer").height();
var voletPlaylistOuvert = false;

// variable globale qui garde en memoire la playlist en cours de lecture
var playlistCourante = {
    playlist_id: null,      //id de la playlist en cours de lecture
    playlist_name: null,    // nom de la playlist en cours de lecture
    tracks: null,           //musiques contenues dans la playlist
    playingTrack: null      //position de la musique en cours de lecture dans la playlist
};

var htmlSize = { h: $("html").height(), w: $("html").width() };
//handler qui recupere la position de la souris constamment
var currentMousePos = { x: -1, y: -1 };
    $(document).mousemove(function(event) {
        currentMousePos.x = event.pageX;
        currentMousePos.y = event.pageY;
    });
    
//fonction qui affiche la liste des playlists depuis le menu principal
function afficherPlaylists()
{
    $("#mainDiv").empty();
    $("#mainDiv").append('<h3>Vos playlists</h3>');
    $("#mainDiv").append('<ul id="userPlaylistsInMain" class="trackList"></ul>');
    
    $.ajax({
        url: 'index.php', //url du script PHP qu'on appelle
        type: 'GET', // Le type de la requête HTTP, ici  GET
        data: 'c=playlist&a=getPlaylists', // c = controlleur PHP a executer, a = methode de ce controlleur a executer, q = recherche
        dataType: 'JSON', //on demande du JSON en retour
        success: function (data) {
            
            if(data.length == 0)
             {
                 //aucune playlist
                 $("#userPlaylistsInMain").append("Aucune playlist");
                 
             }
             else
             {
                 //afficher les playlists
                console.log(data);
                 
                 for(var i=0; i < data.length ; i++)
                 {
                     
                     
                     $("#userPlaylistsInMain").append('<li class="playLi" id="playLiIdInMain'+data[i].playlist_id+'" data-name="'+data[i].playlist_name+'"><p onclick="chargerPlaylist('+data[i].playlist_id+', true)">'+data[i].playlist_name+'</p><img src="css/icons/bin.png" class="iconBtn" onclick="delPlaylist('+data[i].playlist_id+', \'playLiIdInMain'+data[i].playlist_id+'\')"/></li>');
                 }
                 
             }
        }
    });
}

//fonction qui supprime les playlists depuis le menu principal
function delPlaylist(playlist_id, elementId){
    
    if(playlistCourante.playlist_id == playlist_id && playlistCourante.playingTrack != null)
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
                     //on met a jour l'affichage des playlists
                    afficherPlaylists();
                    
                    //si on a supprimé une playlist qui était en cours de lecture, on réinitialise la variable globale playlistCourante
                    if(playlist_id == playlistCourante.playlist_id)
                    {
                        playlistCourante.playlist_id = null;
                        playlistCourante.playlist_name = null;
                        playlistCourante.tracks = null;
                        
                        $("#playlistInfos").empty();
                        $("#playlistInfos").attr("data-playlist-id", -1);
                        
                        var tracks = $("#voletPlaylist").children(".trackList");
                        tracks.empty();
                    }
                 }
             }
         });
    }
}

//fonction pour charger une playlist dans le volet de playlist et dans la variable globale playlistCourante. "lancerApresChargement" definit s'il faut lancer la premiere
//musique de la playlist dans le lecteur audio une fois le chargement terminé
function chargerPlaylist(playlist_id, lancerApresChargement)
{
    console.log(playlist_id);
    
    $.ajax({
        url: 'index.php', //url du script PHP qu'on appelle
        type: 'GET', // Le type de la requête HTTP, ici  GET
        data: 'c=playlist&a=getPlaylist&id='+playlist_id, // c = controlleur PHP a executer, a = methode de ce controlleur a executer
        dataType: 'JSON', //on demande du JSON en retour
        success: function (data) {
            //console.log(data);
            if(data != false)
             {
                 playlistCourante.playlist_id = data.playlist_id;
                 playlistCourante.playlist_name = data.playlist_name;
                 playlistCourante.tracks = data.tracks;
                 
                 $("#playlistInfos").empty();
                 $("#playlistInfos").append(data.playlist_name);
                 $("#playlistInfos").attr("data-playlist-id",playlist_id);
                 
                 var tracks = $("#voletPlaylist").children(".trackList");
                 tracks.empty();
                 
                 for(var i = 0; i < data.tracks.length; i++)
                 {
                     if (data.tracks[i].title != null) {
                         
                         title_esc = data.tracks[i].title.replace(/'/g, "\\'");
                         name_esc = data.tracks[i].name.replace(/'/g, "\\'");
                         url_esc = data.tracks[i].mp3_url.replace(/'/g, "\\'");
                         
                            var playBtn = '<img src="css/icons/play.png " class="iconBtn" data-id="' + data.tracks[i].track_id + '" data-position="'+i+'" onclick="lireDepuisPlaylist(' + data.tracks[i].track_id + ',\'' + title_esc + '\',\'' + name_esc + '\','+data.tracks[i].artist_id+',\''+url_esc+'\','+i+')"/>';

                            var addToPlaylistBtn = '<img src="css/icons/addToPlaylist.png " class="iconBtn" data-id="' + data.tracks[i].track_id + '" onclick="addToPlaylist(' + data.tracks[i].track_id + ',\'' + title_esc + '\',\'' + name_esc + '\','+data.tracks[i].artist_id+',\''+url_esc+'\')"/>';

                            var favBtn = '<img src="css/icons/fav.png " class="iconBtn" data-id="' + data.tracks[i].track_id + '" onclick="addToFavs(' + data.tracks[i].track_id + ')"/>';
                            var delBtn = '<img src="css/icons/bin.png" class="iconBtn" data-id="'+data.tracks[i].track_id+'" data-position="'+i+'" onclick="delFromPlaylist('+data.tracks[i].track_id+','+data.playlist_id+','+i+',\'trackLiInPlaylist'+i+'\')"/>';

                            var artist = "";

                            if (data.tracks[i].name != null) {
                                artist = '<a class="trackList_artist" onclick="viewArtistPageFromPlaylist(' + data.tracks[i].artist_id + ')">' + data.tracks[i].name + '</a>';
                            }

                            tracks.append('<li id="trackLiInPlaylist'+i+'" data-track-id="'+data.tracks[i].track_id+'" data-position="'+i+'"><div class="trackActions">' +delBtn+ favBtn + addToPlaylistBtn + playBtn + '</div><div class="trackInfos">' + data.tracks[i].title + artist + '</div></li>');
                        }
                 }
                 
                 if(lancerApresChargement)
                 {
                     playlistCourante.playingTrack = 0;
                     //console.log(playlistCourante);
                     lireDepuisPlaylist(data.tracks[0].track_id,data.tracks[0].title,data.tracks[0].name,data.tracks[0].artist_id,data.tracks[0].mp3_url, 0);
                    
                 }
                 
                 surlignerMusiqueEnCours();
             }
        }
    });
}

//fonction de suppression d'une musique d'une playlist
function delFromPlaylist(track_id, playlist_id, position, elementId)
{
    //si on ecoute actuellement cette musique depuis cette playlist, alors erreur
    if(playlistCourante.playlist_id == playlist_id && playlistCourante.playingTrack == position)
    {
        console.log(elementId);
        $("#"+elementId).notify("Impossible de retirer cette musique de la playlist, car elle est en cours de lecture");
    }
    else
    {
        $.ajax({
         url : 'index.php', //url du script PHP qu'on appelle
         type : 'GET', // Le type de la requête HTTP, ici  GET
         data : 'c=playlist&a=delTrackFromPlaylist&pos='+position+'&plid='+playlist_id,
         dataType : 'JSON', //on demande du JSON en retour
         success: function(data){

            //si la modification a été faite avec succes
             if(data != false)
             {
                 //si on vient de modifier une playlist en cours de lecture, on la met a jour dans le volet
                 if(playlistCourante.playlist_id == playlist_id)
                 {
                     //si on a supprimé une musique positionnée avant celle en cours de lecture, on decalle playlistCourante.playingTrack de -1
                     if(position < playlistCourante.playingTrack)
                     {
                         playlistCourante.playingTrack--;
                     }
                     
                     chargerPlaylist(playlist_id, false);
                 }
                 
             }
         }
     });
    }
}

//fonction pour surligner la musique en cours de lecture dans la playlist
function surlignerMusiqueEnCours()
{
    var count = 0;
    $("#voletPlaylist").children(".trackList").children("li").each(function(){
        if(count == playlistCourante.playingTrack)
        {
            console.log(count+"i"+$(this).attr("data-position"));
            $(this).css( "border-style", "solid" );
            $(this).css( "border-width", "1px" );
            $(this).css( "border-color", "#B1FF00" );
        }
        else
        {
            
            $(this).css( "border-style", "none" );
            $(this).css( "border-width", "0px" );
            $(this).css( "border-color", "#B1FF00" );
        }
        count++;
    });
}

//passe a la musique suivante de la playlist
function nextTrack()
{
    playlistCourante.playingTrack = playlistCourante.playingTrack + 1;
    
    if(playlistCourante.playingTrack >= playlistCourante.tracks.length)
    {
        playlistCourante.playingTrack = 0;
    }
    
    var index = playlistCourante.playingTrack;
    surlignerMusiqueEnCours()
    lireDepuisPlaylist(playlistCourante.tracks[index].track_id, playlistCourante.tracks[index].title, playlistCourante.tracks[index].name, playlistCourante.tracks[index].artist_id, playlistCourante.tracks[index].mp3_url, playlistCourante.playingTrack);
    
}

//passe a la musique precedente de la playlist
function previousTrack()
{
    playlistCourante.playingTrack = playlistCourante.playingTrack - 1;
    
    if(playlistCourante.playingTrack < 0)
    {
        playlistCourante.playingTrack = playlistCourante.tracks.length - 1;
    }
    
    var index = playlistCourante.playingTrack;
    surlignerMusiqueEnCours()
    lireDepuisPlaylist(playlistCourante.tracks[index].track_id, playlistCourante.tracks[index].title, playlistCourante.tracks[index].name, playlistCourante.tracks[index].artist_id, playlistCourante.tracks[index].mp3_url, playlistCourante.playingTrack);
    
}

function DivInscription(){
   $("#mainDiv").empty();
    
   $("#mainDiv").append('<form class="inscription" onsubmit="return register()"><h2>Inscription</h2>' +
       '<label for="username" class="left">Username</label><input class="right" id="username" type="text" placeholder="Identifiant"/><br>' +
       '<label for="email" class="left">Email</label><input class="right" id="email" type="email" placeholder="Votre adrese email"/><br>' +
       '<label for="password" class="left">Mot de passe</label><input class="right" id="password" type ="password" placeholder="Mot de passe"/><br>' +
       '<label for="passwordcheck" class="left">Confirmation</label><input class="right" id="passwordcheck" type = "password" placeholder="Confirmation du mot de passe"/><br>' +
       '<button type ="submit" class="bouton right buttonConnection">M\'inscrire</button>' +
   '</form>');

}

function DivConnexion(){
     $("#mainDiv").empty();
    
   $("#mainDiv").append('<form class="connexion" onsubmit="return login()"><h2>Connexion</h2>' +
   '<label for="username" class="left">Username</label><input class="right" id="username" type="text" placeholder="Identifiant"/><br>' +
   '<label for="password" class="left">Mot de passe</label><input class="right" id="password" type ="password" placeholder="Mot de passe"/><br>' +
   '<button type ="submit" class="bouton right buttonConnection">Se connextion</button>' +
   '</form>');
    
}

//affichage / fermeture du volet de playlist
$("#entetePlaylist").click(function(){
        
        var h = $("html").height() - 276;
        
        if(footerHeight == $("footer").height())
        {
            
            $("#voletPlaylist").animate({
                height: '+='+h+'px'
                }, 500, function(){
                    $("#entetePlaylistBtn").attr("src", $("#entetePlaylistBtn").attr("data-src-down"));     
                });
            voletPlaylistOuvert = true;
        }
        else
        {
            
            $("#voletPlaylist").animate({
                height: '-='+h+'px'
                }, 500, function(){
                    $("#entetePlaylistBtn").attr("src", $("#entetePlaylistBtn").attr("data-src-up"));
                });
            voletPlaylistOuvert = false;    
            
        }
        

    });

//gestion responsive du volet playlist et de la pop up des playlists
$( window ).resize(function(){
    
    if(!(footerHeight == $("footer").height()))
    {
          
        $("#voletPlaylist").height($("html").height() - 276);
    }
    
    if($("#playlistsPopup").height() + $("#playlistsPopup").offset().top + $("footer").height() + 10 > $("html").height())
    {
        $("#playlistsPopup").height($("html").height() - $("#playlistsPopup").offset().top - $("footer").height() - 10);
    }
    else if($("#playlistsPopup").height() < 400)
    {
        $("#playlistsPopup").height($("html").height() - $("#playlistsPopup").offset().top - $("footer").height() - 10);
    }
    
    if($("#playlistsPopup").width() + $("#playlistsPopup").offset().left + 10 > $("html").width())
    {
        $("#playlistsPopup").offset({ left: $("html").width() - $("#playlistsPopup").width() - 10 });
    }
    else if($("#playlistsPopup").width() < 400)
    {
        $("#playlistsPopup").offset({ left: $("html").width() - $("#playlistsPopup").width() - 10 });
    }
});

function register(){
    $.ajax({
        type: "POST",
        url: "index.php?c=user&a=register",
        data: { username: $("#username").val(),
            email: $("#email").val(),
            password: $("#password").val(),
            passwordcheck : $("#passwordcheck").val()
        },
        dataType: 'JSON'
    })
    .done(function( msg ) {
        if(msg.status == 0){
            $("#usernameProfile").html("Bienvenue " + $("#username").val());
            $("#buttonConnexion").hide();
            $("#buttonInscription").hide();
            $("#buttonDeconnexion").show();
            $("#mainDiv").html("Vous êtes maintenant inscrit");
        }else{
            for(var err in msg.errors){
                switch (msg.errors[err].id){
                    case 10 :
                        $("#email").notify("Email incorrect", {
                            elementPosition: 'right', className: "error", hideDuration: 10000});
                        break;
                    case 11:
                        $("#email").notify("Email déjà utilisé", {
                            elementPosition: 'right', className: "error", hideDuration: 10000});
                        break;
                    case 12:
                        $("#username").notify("Username incorrect", {
                            elementPosition: 'right', className: "error", hideDuration: 10000});
                        break;
                    case 13:
                        $("#username").notify("Username déjà utilisé", {
                            elementPosition: 'right', className: "error", hideDuration: 10000});
                        break;
                    case 14:
                        $("#passwordcheck").notify("Mots de passe différents", {
                            elementPosition: 'right', className: "error", hideDuration: 10000});
                        break;
                    case 15:
                        $("#password").notify("Mot de passe trop faible (au moins 8 caractères)", {
                            elementPosition: 'right', className: "error", hideDuration: 10000});
                        break;
                }
            }

        }
    })
    .fail(function(msg){
        if(msg.status == -1){
            $(".buttonConnection").notify("Serveur indisponible", { elementPosition: 'right', className: "error" });
        }else{
            $(".buttonConnection").notify("Erreur inconnu", { elementPosition: 'right', className: "error" });
        }
    });
    return false;
};

function login(){
    $.ajax({
        type: "POST",
        url: "index.php?c=user&a=login",
        data: {
            username: $("#username").val(),
            password: $("#password").val()
        },
        dataType: 'JSON'
    })
        .done(function( msg ) {
            if(msg.status == 0){
                console.log(msg.user);
                $("#usernameProfile").html("Bienvenue " + $("#username").val());
                $("#buttonConnexion").hide();
                $("#buttonInscription").hide();
                $("#buttonDeconnexion").show();
                $("#mainDiv").html("Vous êtes maintenant connecté");
            }else{
                $(".buttonConnection").notify("Combinaison incorrect", { elementPosition: 'right', className: "error" });
            }
        })
        .fail(function(msg){
            if(msg.status == -1){
                $(".buttonConnection").notify("Serveur indisponible", { elementPosition: 'right', className: "error" });
            }else{
                $(".buttonConnection").notify("Erreur inconnu", { elementPosition: 'right', className: "error" });
            }
        });
    return false;
};

function disconnect(){
   
        
    $.ajax({
        type: "GET",
        url: "index.php?c=user&a=disconnect"
    })
        .done(function() {
           location.reload();
        })
        .fail(function(){
            
        });
}