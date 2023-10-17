<?php

require './Concerto.php';

//CREAZIONE DATABASE E TABELLA
$campi = [
    'codice',
    'titolo',
    'descrizione',
    'data_concerto'
];
$database_name = 'db_prova';
$table_name = 'concerti';
$filename = "config.txt";
$dbconnection = ConnectionManagement::ConnectToHost($filename);
ConnectionManagement::Create($campi, $filename, $dbconnection);
$dbconnection = ConnectionManagement::ConnectToDB($filename);
ConnectionManagement::CloseConnection($dbconnection);

$data_concerto = new DateTime('now');
$dati = [
    'codice' => 1234,
    'titolo' => 'Concerto1',
    'descrizione' => 'molto bello',
    'data_concerto' => $data_concerto,
];
$concerto = new Concerto($dati);
Concerto::Create($dati, $filename);
$array_values = Concerto::ToArray($concerto->show($filename));
Concerto::Create($array_values, $filename);
