<?php

require './Concerto.php';

$dati = [
    'codice' => 1234,
    'titolo' => 'Concerto1',
    'descrizione' => 'molto bello',
    'data' => new DateTime(),
];
$concerto = new Concerto($dati);

Concerto::Create($concerto->show());