var footerHeight = $("footer").height();

var htmlSize = { h: $("html").height(), w: $("html").width() };
//handler qui recupere la position de la souris constamment
var currentMousePos = { x: -1, y: -1 };
    $(document).mousemove(function(event) {
        currentMousePos.x = event.pageX;
        currentMousePos.y = event.pageY;
    });

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