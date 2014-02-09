<?php
# Datenbank Verbindung herstellen und Datenbank wechseln
# SQLITE CONNECT
include('sqlite_connect.inc.php');

# setzen des einzulesenden Files bald aus der kommandozeile
$first_param = $_SERVER['argv'][1];
echo $first_param;
$original_csv = $first_param;
$inverted_csv = "inverted_".$original_csv;
$retval = system("tac $original_csv > '$inverted_csv'");
$csvfile = $inverted_csv;

# Einfache Funktion welche Variablen in die Umsätze DB schreibt
function intodatabase($datum,$betrag,$kontakt,$bemerkung){
# Hier ein Query SQLite
$query = "INSERT INTO umsaetze SET datum='$datum',betrag='$betrag',destination='$kontakt',bemerkung='$bemerkung'";
echo $query."\n";
$result = $db->query($query);
}

# Funktion die die Datensätze an der Richtigen stelle einfügt
function check_Insert($csvfile) {
  # Variable $var declarieren
    $var = 0;
  # Query des letzten Eintrags in der DatenBank sortiert nach Datum
    $query_check = "SELECT umsatz_id,datum,destination FROM umsaetze ORDER BY umsatz_id DESC LIMIT 1";
    $results = $db->query($query_check);
   while($arRow = $results->fetchArray())
      {
  # zuweisen der ergebnissatzID auf eine eindimensionale Variablen / Sichern für den Vergleich
  $end_id = $arRow['umsatz_id'];
  $end_datum = $arRow['datum'];
  $end_destination = $arRow['destination'];
      }
  # Funktionsaufruf Fileeinlesen
    $csvhandle = fopen($csvfile, "r");
  # Variable welche als insertzeiger dient initialisieren
  $nowinsert = 0;
  # Ausgabe des letzten Vorhandenen datensatzes vor dem neuen Insert
  echo $end_datum."\n";
  echo $end_destination."\n";
  # Einlesen des CSVs mit Übergabe an insertfunktion wenn neue Datensätze erreicht
   while (($data = fgetcsv($csvhandle, 300, ";")) !== FALSE) 
      	 {
		$num = count($data);
#		print_r($data);
        	
		$datum = $data[2]; if($datum == "Valutadatum") {continue;}
		# patch nachdem die Sparkasse in ihren CSVs 2013 statt 13 benutzt
                $teil2 = substr($datum, -2); $teil1 = substr($datum, 0, 6); $datum = $teil1; $datum .= $teil2;
		$bemerkung = trim($data[4]);
		$kontakt = trim($data[5]);
		$betrag = $data[8];
		# Check ob Datensatz neu sobald einmal aktiviert bleibt es auf aktiv
		if($nowinsert == 1){ echo "EINTRAG IN DB: "; intodatabase($datum,$betrag,$kontakt,$bemerkung);}
# echo "Check: ".$datum." vs. Enddatum: ".$end_datum." Kontakt: ".$kontakt." vs. Endkontakt: ".$end_destination." -> Variable nowinsert -> ".$nowinsert."\n";
		# eigentlicher Check ob aktueller Durchlauf dem letzten eintrag in der Bank entspricht
		if($datum == $end_datum){ if($kontakt == $end_destination) { $nowinsert = 1;}}
	 }	
# schließen des Filehandles
fclose($csvhandle);

# experimentelle Rückgabewerte ohne Bedeutung
if ($end_id == $num) {return TRUE;}
else {return FALSE;}
	  
}


# Mainfunktion 

check_Insert($csvfile);
?>


