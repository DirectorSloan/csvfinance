<?php
# Projekt csvfinance 2013
# written by Ben Langenberg ben.langenberg@bencarsten.de
# Init File um sqlite Verbindung herzustellen

$filename = "sparkasse.csv";

if (!file_exists($filename)) {
	fopen($filename, "w+");	
} 

$db = new SQLite3($filename, SQLITE3_OPEN_READWRITE);

if (filesize($filename) == "0") {
$createquery = "CREATE TABLE umsaetze ( umsatz_id INT AUTO_INCREMENT PRIMARY KEY NOT NULL, datum VARCHAR(15), destination VARCHAR(30), bemerkung VARCHAR(50), betrag INT )";
$result = $db->query($createquery);
}
?>

