<?php
error_reporting(0);
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
$arTankstellen = array('JET', 'ARAL', 'TOTAL', 'HEM', 'AGIP', 'BFT', 'Gulf');
$arBargeld = array('GA NR');
$arEinkaufen = array('KAUFLAND', 'REWE', 'HIT', 'KONSUM', 'PERFETTO');
$i = 0;
if($_GET['suchenach'] == 'Tankstellen')
  {
    foreach($arTankstellen as $Tankstellen)
      { $arAlles[$i] = search("destination", $Tankstellen); $i++; }
  }
elseif($_GET['suchenach'] == 'Bargeld')
  {
    foreach($arBargeld as $Bargeld)
      { $arAlles[$i] = search("destination", $Bargeld); $i++; }
  }
elseif($_GET['suchenach'] == 'Einkauf')
  {
    foreach($arEinkaufen as $Einkauf)
      { $arAlles[$i] = search("destination", $Einkauf); $i++; }
  }

# Summe der Rechnung einer Klasse
foreach($arAlles as $Elements){
  foreach($Elements as $Kinder)
    { $Summe += $Kinder['betrag']; }
}
echo "Summe: ".$Summe."\n";

# Mainfunktion 
$suchenach = "";
if($_GET['suchenach']) { $suchenach = $_GET['suchenach']; }
echo $suchenach;
if($suchenach != "")
{
$arResults = search("destination", $suchenach); echo "<pre>"; print_r($arResults); echo "</pre>";
echo "<pre>"; print_r($arAlles); echo "</pre>";
}
echo "<html>";
echo "<form action='show.php'>Kategorien: <input name='suchenach' type='text' size='30'></input><br>";
echo "Ab: T <input name='vonTag' type='text' size='5'></input>";
echo "M <input name='vonMonat' type='text' size='5'</input>";
echo "J <input name='vonJahr' type='text' size='5'</input><br>";
echo "Bis: T <input name='bisTag' type='text' size='5'></input>";
echo "M <input name='bisMonat' type='text' size='5'</input>";
echo "J <input name='bisJahr' type='text' size='5'</input><br>";
echo "</form>";
echo "</html>";
print_r($arResults);
?>