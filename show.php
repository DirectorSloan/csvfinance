<?php
# Datenbankverbindung vorkonfigurieren
include('connect.inc.php');

# Such-Rückgabefunktion nach bestimmten Kontakten
function search($attribute, $filter){
if($filter == "") die;
$select_query = "SELECT * FROM umsaetze ";
$search_query = "WHERE $attribute LIKE '%$filter%'";
$complete_query = "$select_query$search_query";
# DEBUG echo "\n".$complete_query."\n";
$result = mysql_query($complete_query);
while ($row = mysql_fetch_array($result)){
$arResults[] = $row;
}
return $arResults;
}


# Definition der Umsatzkategorien
$arTankstellen = array('JET', 'ARAL', 'TOTAL', 'HEM', 'AGIP', 'BFT');
$i = 0;
foreach($arTankstellen as $Tankstellen)
  { $arAlles[$i] = search("destination", $Tankstellen); $i++;
  }
# Summe der Rechnung einer Klasse
foreach($arAlles as $Elements){
  foreach($Elements as $Kinder)
    { $Summe += $Kinder['betrag']; }
}
echo "Summe: ".$Summe;

# Mainfunktion 
#$suchenach = "";
#if($_GET['suchenach']) { $suchenach = $_GET['suchenach']; }
#echo $suchenach;
#if($suchenach != "")
#{
#$arResults = search("destination", $suchenach); echo "<pre>"; print_r($arResults); echo "</pre>";
#echo "<pre>"; print_r($arAlles); echo "</pre>";
#}
#echo "<html>";
#echo "<form action='show.php'>Kategorienr: <input name='suchenach' type='text' size='30'></input></form>";
#echo "</html>";
# print_r($arResults);
?>