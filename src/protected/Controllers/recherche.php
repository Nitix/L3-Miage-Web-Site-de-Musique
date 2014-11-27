<?php

$connexion = mysql_connect("romainpapelier.fr.mysql","romainpapelier_","ixktis");
$bdd = mysql_select_db("romainpapelier_", $connexion);
$queryT = "select * from tracks where title like '%" . $_GET["term"] . "%'order by title";
$queryA = "select * from artists where name like '%" . $_GET["term"] . "%'order by name";

$requestT = mysql_query($queryT);
$resT = mysql_fetch_assoc($requestT);

$requestA = mysql_query($queryA);
$resA = mysql_fetch_assoc($requestA);

$arrayT = array();
$arrayA = array();

while($resT != false)
{
    array_push($arrayT, $resT);
    $resT = mysql_fetch_assoc($requestT);
}

while($resA != false)
{
    array_push($arrayA, $resA);
    $resA = mysql_fetch_assoc($requestA);
}

$retour = array();
$retour["musiques"] = $arrayT;
$retour["artistes"] = $arrayA;

echo json_encode($retour);

?>