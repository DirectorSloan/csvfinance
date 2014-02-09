<?php
# Projekt csvfinance 2013
# written by Ben Langenberg ben.langenberg@bencarsten.de
# Init File um sqlite Verbindung herzustellen

$filename = 'sparkasse.csv';
$createquery = "CREATE TABLE umsaetze ( datum VARCHAR(15), destination VARCHAR(30), bemerkung VARCHAR(50), betrag INT )";

if (!file_exists($filename)) {
	fopen($filename, "w+");	
} 

class BenSQLite extends SQLite3
{


  function __construct()
  {
    $this->open('sparkasse.csv');
  }

}


if (filesize($filename) == "0") {
  $db =  new BenSQLite();
  $db->query($createquery);
}
?>

