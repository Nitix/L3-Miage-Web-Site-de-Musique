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
    $("#mainDiv").append('<ul id="userPlaylistsInMain" class="trackList"></ul>');
    
    $.ajax({
        url: 'index.php', //url du script PHP qu'on appelle
        type: 'GET', // Le type de la requête HTTP, ici  GET
        data: 'c=guest&a=getPlaylists', // c = controlleur PHP a executer, a = methode de ce controlleur a executer, q = recherche
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
             data : 'c=guest&a=delPlaylist&id='+playlist_id,
             dataType : 'JSON', //on demande du JSON en retour
             success: function(data){
                 
                 if(data == true)
                 {
                    afficherPlaylists();
                    
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
                     console.log(playlistCourante);
                     lireDepuisPlaylist(data.tracks[0].track_id,data.tracks[0].title,data.tracks[0].name,data.tracks[0].artist_id,data.tracks[0].mp3_url, 0);
                    
                 }
                 
                 surlignerMusiqueEnCours();
             }
        }
    });
}

function delFromPlaylist(track_id, playlist_id, position, elementId)
{
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
         data : 'c=guest&a=delTrackFromPlaylist&pos='+position+'&plid='+playlist_id,
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