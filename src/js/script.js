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