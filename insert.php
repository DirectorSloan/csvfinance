<?php
# Datenbank Verbindung herstellen und Datenbank wechseln
include('connect.inc.php');

# setzen des einzulesenden Files bald aus der kommandozeile
$first_param = $_SERVER['argv'][1];
echo $first_param;
$csvfile = "20130806-1801133650-umsatz_inverted.csv";

# SQL Syntax um die Umsaetze Tabelle zu erstellen 
//create table umsaetze ( umsatz_id INT AUTO_INCREMENT PRIMARY KEY NOT NULL, datum VARCHAR(15), destination VARCHAR(30), bemerkung VARCHAR(50), betrag INT );

# Einfache Funktion welche Variablen in die Umsätze DB schreibt
function intodatabase($datum,$betrag,$kontakt,$bemerkung){
$query = "INSERT INTO umsaetze SET datum='$datum',betrag='$betrag',destination='$kontakt',bemerkung='$bemerkung'";
echo $query."\n";
$result = mysql_query($query);
}

# Funktion die die Datensätze an der Richtigen stelle einfügt
function check_Insert($csvfile) {
  # Variable $var declarieren
    $var = 0;
  # Query des letzten Eintrags in der DatenBank sortiert nach Datum
    $query_check = "SELECT umsatz_id,datum,destination FROM umsaetze ORDER BY umsatz_id DESC LIMIT 1";
    $result = mysql_query($query_check);
  while($arRow = mysql_fetch_array($result))
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
		# DEBUG       print_r($data);
        	
		$datum = $data[2]; if($datum == "Valutadatum") {continue;}
		$bemerkung = trim($data[4]);
		$kontakt = trim($data[5]);
		$betrag = $data[8];
		# Check ob Datensatz neu sobald einmal aktiviert bleibt es auf aktiv
		if($nowinsert == 1){ echo "EINTRAG IN DB: "; intodatabase($datum,$betrag,$kontakt,$bemerkung);}
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


