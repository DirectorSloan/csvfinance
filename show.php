<?php
error_reporting(0);
# Datenbankverbindung vorkonfigurieren
include('connect.inc.php');

# Deklarieren der Abzufangenden Variablen
if ($_GET['vonTag']) {$vonTag = $_GET['vonTag']; }
if ($_GET['vonMonat']) {$vonMonat = $_GET['vonMonat']; }
if ($_GET['vonJahr']) {$vonJahr = $_GET['vonJahr']; }
if ($_GET['bisTag']) {$bisTag = $_GET['bisTag']; }
if ($_GET['bisMonat']) {$bisMonat = $_GET['bisMonat']; }
if ($_GET['bisJahr']) {$bisJahr = $_GET['bisJahr']; }

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
$arEinkaufen = array('KAUFLAND', 'REWE', 'HIT', 'KONSUM', 'PERFETTO', 'MUELLER', 'MARKTKAUF');
$arShoppen = array('AMAZON', 'ADVANZIA', 'MEDIA MARKT', 'SATURN', 'C&A', 'VERO MODA', 'H&M', 'IKEA', 'RUNNERS');
$arFixkosten = array('RUNDFUNK ARD', 'KABEL DEU', 'HUK', 'LEIPZIGER WOHNUNGS', 'E-PLUS', 'DEVK', 'TREUHANDKONTO');
$arKomplett = array('%');
$i = 0;

function checkthedate($Kinder, $vonTag, $vonMonat, $vonJahr, $bisTag, $bisMonat, $bisJahr){

if($_GET['vonMonat'] != "") 
  {
    $datum = $Kinder['datum'];
    $arDatum = explode('.', $datum);
    $arTag = $arDatum[0];
    $arMonat = $arDatum[1];
    $arJahr = $arDatum[2];
    $Timestamp = mktime(0, 0, 0, $arMonat, $arTag, $arJahr);

    $TSvon = mktime(0, 0, 0, $vonMonat, $vonTag, $vonJahr);
    $TSbis = mktime(0, 0, 0, $bisMonat, $bisTag, $bisJahr);

#    echo "Timestamp Datensatz : ".$Timestamp." - Timestamp von: ".$TSvon." - Timestamp bis: ".$TSbis."<br>";
#    echo "<pre>"; print_r($arDatum); echo "</pre>";
#    echo $vonTag; echo ":"; echo $arTag;
# Check ob das aktuelle Element im Zeitrahmen steckt
  if(($Timestamp >= $TSvon) AND ($Timestamp <= $TSbis))
    { return TRUE; }
   else
    { return FALSE; }
  }
    
else { return TRUE; }  

}

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
elseif($_GET['suchenach'] == 'Komplett')
  {
    foreach($arKomplett as $Komplett)
	    { $arAlles[$i] = search("destination", $Komplett); $i++; }
  }
elseif($_GET['suchenach'] == 'Shoppen')
  {
    foreach($arShoppen as $Shoppen)
      { $arAlles[$i] = search("destination", $Shoppen); $i++; }
  }
elseif($_GET['suchenach'] == 'Fixkosten')
  {
    foreach($arFixkosten as $Fixkosten)
      { $arAlles[$i] = search("destination", $Fixkosten); $i++; }
  }


# Summe der Rechnung einer Klasse
if($_GET['calculate'])
  {
 foreach($arAlles as $Elements){
  foreach($Elements as $Kinder)
    { 
     $return_datum = checkthedate($Kinder, $vonTag, $vonMonat, $vonJahr, $bisTag, $bisMonat, $bisJahr);
#     echo $return_datum;
     if($return_datum == TRUE)
       { 
	 echo "ID: ".$Kinder['umsatz_id']." - Betrag: ".$Kinder['betrag']." Euro - Datum: ".$Kinder['datum']." - Destination: ".$Kinder['destination']."<br>";
	 $Summe += $Kinder['betrag']; 
       }
      
    }
}
echo "\n";
echo "Summe: ".$Summe." Euro \n";
  }

# Visualisierungsmodul
if($_GET['visualize'])
  {
$im = ImageCreate (1200, 3600)
      or die ("Kann keinen neuen GD-Bild-Stream erzeugen");
$background_color = ImageColorAllocate ($im, 200, 200, 200);
$text_color = ImageColorAllocate ($im, 233, 14, 91);
foreach($arAlles as $Elements) {
  foreach($Elements as $Kinder) {
   $betrag = $Kinder['betrag'];
#   echo $betrag."<br>";
   ImageRectangle ($im, $i, 0, $i, $betrag, $text_color);
   $i++;
  }
}
ImagePNG ($im, 'test/test.png');
echo "<img src='test/test.png'>BILD</img>";
#system("rm -f test/test.png");

}


# Mainfunktion 
$suchenach = "";
if($_GET['suchenach']) { $suchenach = $_GET['suchenach']; }
#echo $suchenach;
if($suchenach != "")
{
$arResults = search("destination", $suchenach); echo "<pre>"; print_r($arResults); echo "</pre>";
#echo "<pre>"; print_r($arAlles); echo "</pre>";
}
echo "<html>";
echo "<form action='show.php'>Kategorien: <select name='suchenach' size='1'><br>";
echo "<option "; if($suchenach == 'Tankstellen') echo "selected "; echo ">Tankstellen</option>";
echo "<option "; if($suchenach == 'Einkauf') echo "selected "; echo ">Einkauf</option>";
echo "<option "; if($suchenach == 'Bargeld') echo "selected "; echo ">Bargeld</option>";
echo "<option "; if($suchenach == 'Shoppen') echo "selected "; echo ">Shoppen</option>";
echo "<option "; if($suchenach == 'Fixkosten') echo "selected "; echo ">Fixkosten</option>";
echo "<option "; if($suchenach == 'Komplett') echo "selected "; echo ">Komplett</option>";
echo "</select><br>";
echo "<input name='calculate' type='checkbox'>Rechnen</input>";
echo "<input name='visualize' type='checkbox'>Visualisieren</input><br>";
echo "Ab: T <select name='vonTag' size=1>"; for($i=1;$i<=31;$i++) { echo "<option "; if($vonTag == $i) echo "selected "; echo ">".$i."</option>"; } echo "</select>";
echo "M <select name='vonMonat' size=1>"; for($j=1;$j<=12;$j++) { echo "<option "; if($vonMonat == $j) echo "selected "; echo ">".$j."</option>"; } echo "</select>";
echo "J <select name='vonJahr' size=1>"; for($k=11;$k<=14;$k++) { echo "<option "; if($vonJahr == $k) echo "selected "; echo ">".$k."</option>"; } echo "</select><br>";
echo "Bis: <select name='bisTag' size=1>"; for($i=1;$i<=31;$i++) { echo "<option "; if($bisTag == $i) echo "selected "; echo ">".$i."</option>"; } echo "</select>";
echo "M <select name='bisMonat' size=1>"; for($j=1;$j<=12;$j++) { echo "<option "; if($bisMonat == $j) echo "selected "; echo ">".$j."</option>"; } echo "</select>";
echo "J <select name='bisJahr' size=1>"; for($k=11;$k<=14;$k++) { echo "<option "; if($bisJahr == $k) echo "selected "; echo ">".$k."</option>"; } echo "</select><br>";
echo "<input type='submit' name='submit' ></input><br>";
echo "</form>";
echo "</html>";
#print_r($arResults);
?>