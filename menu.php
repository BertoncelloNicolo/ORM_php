<?php

require './Concerto.php';
require './ORM.php';

$fine = false;
$filenmame = 'config.txt';
$codice=0;
$titolo=" ";
$descrizione="";
$data_concerto;

while (!$fine) {
    print " SCEGLI L'OPERAZIONE DA FARE DUL DATABASE";
    print "premere 1 per crrare un record ";
    print "premere 2 per mostrare un record";
    print "premere 3 per modificare un record ";
    print "premere 4 per eliminare un record";
    print "premere 5 per mostrare tutti i record presenti nella tabella";
    print "premere 0 per terminare il programma";

    $scelta = readline(); {
        switch ($scelta) {
            case "1": //CREAZIONE RECORD
                print "inserisci codice";
                $codice=readline();  //inserimento codice 
                print "inserisci titolo";
                $titolo=readline(); //inserimento titolo
                print "inserisci descrizione";
                $descrizione=readline(); //inserimento descrizione
                print "inserisci la data del concerto";
                $data_concerto=readline(); //inserimento data concerto 
 
                // immagazino i dati dentro array 
                $dati = [ 
                    'codice' => $codice,
                    'titolo' => $titolo,
                    'descrizione' => $descrizione,
                    'data_concerto' => $data_concerto,
                ];
                Concerto::Create($dati,$filename); //metodo create in cui passo array 
                break; 
            case "2":
                break; //mostra record 
            case "3":
                break; //modifica record 
            case "4":
                break; // elimina record 
            case "5":
                break; // mostra tutti record presenti
            case "0":
                $fine = true;
                break; //esci dal programma 
        }
    }
}

/* private int $codice;
    private string $titolo;
    private string $descrizione;
    private $data_concerto;
    */