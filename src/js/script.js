var footerHeight = $("footer").height();

var playlistCourante = {
    playlist_id: null,
    playlist_name: null,
    tracks: null,
    playingTrack: null
};

var htmlSize = { h: $("html").height(), w: $("html").width() };
//handler qui recupere la position de la souris constamment
var currentMousePos = { x: -1, y: -1 };
    $(document).mousemove(function(event) {
        currentMousePos.x = event.pageX;
        currentMousePos.y = event.pageY;
    });
    
function afficherPlaylists()
{
    $("#mainDiv").empty();
    $("#mainDiv").append('<h3>Vos playlists</h3>');
    $("#mainDiv").append('<ul id="userPlaylists" class="trackList"></ul>');
    
    $.ajax({
        url: 'index.php', //url du script PHP qu'on appelle
        type: 'GET', // Le type de la requête HTTP, ici  GET
        data: 'c=guest&a=getPlaylists', // c = controlleur PHP a executer, a = methode de ce controlleur a executer, q = recherche
        dataType: 'JSON', //on demande du JSON en retour
        success: function (data) {
            
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
                     $("#userPlaylists").append('<li class="playLi" data-name="'+data[i].playlist_name+'"><p onclick="chargerPlaylist('+data[i].playlist_id+', true)">'+data[i].playlist_name+'</p><img src="css/icons/bin.png" class="iconBtn" onclick="delPlaylist('+data[i].playlist_id+')"/></li>');
                 }
                 
             }
        }
    });
}

function delPlaylist(playlist_id){
    
    $.ajax({
         url : 'index.php', //url du script PHP qu'on appelle
         type : 'GET', // Le type de la requête HTTP, ici  GET
         data : 'c=guest&a=delPlaylist&id='+playlist_id,
         dataType : 'JSON', //on demande du JSON en retour
         success: function(data){
             
             if(data == true)
             {
                afficherPlaylists();
             }
         }
     });
    
}

function chargerPlaylist(playlist_id, lancerApresChargement)
{
    console.log(playlist_id);
    
    $.ajax({
        url: 'index.php', //url du script PHP qu'on appelle
        type: 'GET', // Le type de la requête HTTP, ici  GET
        data: 'c=guest&a=getPlaylist&id='+playlist_id, // c = controlleur PHP a executer, a = methode de ce controlleur a executer
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
                            var playBtn = '<img src="css/icons/play.png " class="iconBtn" data-id="' + data.tracks[i].track_id + '" data-position="'+i+'" onclick="lireDepuisPlaylist(' + data.tracks[i].track_id + ',\'' + data.tracks[i].title + '\',\'' + data.tracks[i].name + '\','+data.tracks[i].artist_id+',\''+data.tracks[i].mp3_url+'\','+i+')"/>';

                            var addToPlaylistBtn = '<img src="css/icons/addToPlaylist.png " class="iconBtn" data-id="' + data.tracks[i].track_id + '" onclick="addToPlaylist(' + data.tracks[i].track_id + ',\'' + data.tracks[i].title + '\',\'' + data.tracks[i].name + '\','+data.tracks[i].artist_id+',\''+data.tracks[i].mp3_url+'\')"/>';

                            var favBtn = '<img src="css/icons/fav.png " class="iconBtn" data-id="' + data.tracks[i].track_id + '" onclick="addToFavs(' + data.tracks[i].track_id + ')"/>';

                            var artist = "";

                            if (data.tracks[i].name != null) {
                                artist = '<a class="trackList_artist" onclick="viewArtistPage(' + data.tracks[i].artist_id + ')">' + data.tracks[i].name + '</a>';
                            }

                            tracks.append('<li data-track-id="'+data.tracks[i].track_id+'"><div class="trackActions">' + favBtn + addToPlaylistBtn + playBtn + '</div><div class="trackInfos">' + data.tracks[i].title + artist + '</div></li>');
                        }
                 }
                 
                 if(lancerApresChargement)
                 {
                     playlistCourante.playingTrack = 0;
                     
                     lire(data.tracks[0].track_id,data.tracks[0].title,data.tracks[0].name,data.tracks[0].artist_id,data.tracks[0].mp3_url);
                     console.log(playlistCourante);
                 }
                 
                 surlignerMusiqueEnCours();
             }
        }
    });
}

function surlignerMusiqueEnCours()
{
    var count = 0;
    $("#voletPlaylist").children(".trackList").children().each(function(){
        if(count == playlistCourante.playingTrack)
        {
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

function nextTrack()
{
    playlistCourante.playingTrack = playlistCourante.playingTrack + 1;
    
    if(playlistCourante.playingTrack >= playlistCourante.tracks.length)
    {
        playlistCourante.playingTrack = 0;
    }
    
    var index = playlistCourante.playingTrack;
    surlignerMusiqueEnCours()
    lire(playlistCourante.tracks[index].track_id, playlistCourante.tracks[index].title, playlistCourante.tracks[index].name, playlistCourante.tracks[index].artist_id, playlistCourante.tracks[index].mp3_url);
    
}

function previousTrack()
{
    playlistCourante.playingTrack = playlistCourante.playingTrack - 1;
    
    if(playlistCourante.playingTrack < 0)
    {
        playlistCourante.playingTrack = playlistCourante.tracks.length - 1;
    }
    
    var index = playlistCourante.playingTrack;
    surlignerMusiqueEnCours()
    lire(playlistCourante.tracks[index].track_id, playlistCourante.tracks[index].title, playlistCourante.tracks[index].name, playlistCourante.tracks[index].artist_id, playlistCourante.tracks[index].mp3_url);
    
}

function DivInscription(){
    $("#mainDiv").empty();
    
   $("#mainDiv").append('<form class ="inscription"><br><input type="text" value="Votre nom"/><br>           <input type ="text" value = "Vvotre prenom"/><br>           <input type="text" value ="choisissez un identifiant"/>           <input type="text" value="Votre adrese email"/><br>           <input type ="text" value ="Choisissez un mot de passe"/><br>           <input type = "text" value ="confirmez votre mot de passe"/><br>           <input type ="submit" value ="m\'inscrire" id="minscrire" class="bouton"/>       </form>');
    
    /*
    var defaut = document.getElementById('divDefaut');
    var conx = document.getElementById('divConx');
    var insc = document.getElementById('divInsc');

    defaut.style.display="none";
    conx.style.display="none";
    insc.style.display="block";*/
}

function DivConnexion(){
     $("#mainDiv").empty();
    
   $("#mainDiv").append('<form class="connexion">            <br><br><br><br>            <span>Identifiant  </span><br>            <input type ="text" value="identifiant"/><br> <br>            <span>Mot de passe</span><br>            <input type = "text" value ="entrez votre mot de passe"/><br>            <br>            <input type="button" value ="Me connecter" class="bouton"/>        </form>');
    /*
    var defaut = document.getElementById('divDefaut');
    var conx = document.getElementById('divConx');
    var insc = document.getElementById('divInsc');

    defaut.style.display="none";
    insc.style.display ="none";
    conx.style.display ="block";*/
    
    
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
            
        }
        else
        {
            
            $("#voletPlaylist").animate({
                height: '-='+h+'px'
                }, 500, function(){
                    $("#entetePlaylistBtn").attr("src", $("#entetePlaylistBtn").attr("data-src-up"));
                });
                
            
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