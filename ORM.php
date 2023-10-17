<?php

require './Concerto.php';

//CREAZIONE DATABASE E TABELLA
$campi = [
    'codice',
    'titolo',
    'descrizione',
    'data'
];
$database_name = 'db_prova';
$table_name = 'concerti';
$filename = "config.txt";
$dbconnection = ConnectionManagement::ConnectToHost($filename);
ConnectionManagement::Create($filename, $campi, $dbconnection);
$dbconnection = ConnectionManagement::ConnectToDB($filename);
ConnectionManagement::CloseConnection($dbconnection);

$dati = [
    'codice' => 1234,
    'titolo' => 'Concerto1',
    'descrizione' => 'molto bello',
    'data' => "2023-10-17",
];
$concerto = new Concerto($dati);
Concerto::Create($dati, $filename);
//Concerto::Create($concerto->show(), $filename);