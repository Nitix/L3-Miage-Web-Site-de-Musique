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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Mon Site - Videos</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="css/style.css"/>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	</head>
	
	<body>
	
		<h1>Recherche</h1>

		<div class="panel">
			<form action="" method="POST">
				<p>
				<input type="text" id="recherche" name="recherche" size="25"/>
				</p>
			</form>
			<button id="btnRecherche">Rechercher</button>
		</div>
		
		<div id="main">
		    
		</div>

        <script src="js/jquery-1.11.1.min.js"></script>
        <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	    <script>
	        $('#recherche').autocomplete({
            	source : 'index.php?c=base&a=autocomplete'
            });

            /*
            $.ajax({
                url: 'index.php?c=base&a=autocomplete&term=a',
                success: function(data){
                    console.log(data);
                }
            });
            */
            
            $("#btnRecherche").click(function(){
                console.log("aaaa");
                $("#main").empty();
                $("#main").append('<img src="http://i0.kym-cdn.com/photos/images/newsfeed/000/103/740/Me%20Gusta.png?1318992465"></img><p>HAAAAAAA</p>');
            });
            
	    </script>
	</body>
</html>