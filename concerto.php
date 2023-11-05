<?php

require_once 'db_manager.php';
require_once 'json_reader.php';

class Concerto
{
    private $id;
    private $codice;
    private $titolo;
    private $descrizione;
    private $dataConcerto;

    public function __construct(array $values = null)
    {
        if ($values != null)
            $this->__setObject($values);
    }

    private function __setObject(array $values)
    {
        $this->__setCodice($values['codice']);
        $this->__setTitolo($values['titolo']);
        $this->__setDescrizione($values['descrizione']);
        $this->__setDataConcerto($values['dataConcerto']);
    }

    private function __getId(): int
    {
        return $this->id;
    }

    private function __setCodice(int $codice)
    {
        $this->codice = $codice;
    }

    private function __getCodice(): int
    {
        return $this->codice;
    }

    private function __getTitolo(): string
    {
        return $this->titolo;
    }

    private function __setTitolo(string $titolo)
    {
        $this->titolo = $titolo;
    }

    private function __getDescrizione(): string
    {
        return $this->descrizione;
    }

    private function __setDescrizione(string $descrizione)
    {
        $this->descrizione = $descrizione;
    }

    private function __getDataConcerto(): DateTime
    {
        if ($this->dataConcerto instanceof DateTime)
            return $this->dataConcerto;
        else
            return new DateTime($this->dataConcerto);
    }

    private function __setDataConcerto($dataConcerto)
    {
        if ($dataConcerto instanceof DateTime)
            $this->dataConcerto = $dataConcerto;
        else
            $this->dataConcerto = new DateTime($dataConcerto);
    }

    static public function Create(array $params): Concerto | null
    {
        try {
            return DbManager::Insert($params);
        } catch (Exception $e) {
            return null;
        }
    }
    public static function Find(int $id): Concerto | null
    {
        try {
            return DbManager::Select($id);
        } catch (Exception $e) {
            return null;
        }
    }
    public static function FindAll(): array | null
    {
        try {
            return DbManager::SelectAll();
        } catch (Exception $e) {
            return null;
        }
    }

    //ritorna array contenente gli attributi in formato chiave valore
    public function Show(): array | null
    {
        $params = [];
        try {
            foreach ($this as $key => $value) {
                $params[$key] = $value;
            }
            return $params;
        } catch (Exception $e) {
            return null;
        }
    }

    //ritorna una stringa formattata per contenere i valori degli attributi
    public function ShowString(): string
    {
        return "\nID: {$this->__getId()}\nCodice: {$this->__getCodice()}\nTitolo: {$this->__getTitolo()}\nDescrizione: {$this->__getDescrizione()}\nData: {$this->__getDataConcerto()->format('Y-m-d H:i:s')}\n\n";
    }

    public function Delete(): Concerto | null
    {
        try {
            return DbManager::Delete($this->__getId());
        } catch (Exception $e) {
            return null;
        }
    }

    public function Update(array $params): Concerto | null
    {
        try {
            return DbManager::Update($params, $this->__getId());;
        } catch (Exception $e) {
            return null;
        }
    }

    public function Sala(): Concerto | null
    {
        try {
            return DbManager::sala($this->__getId()); //vado  a prendere l'id dell'oggetto che voglio e lo metto dentro al metodo sala 
        } catch (Exception $e) {
            return null;
        }
    }
}


if (isset($argv[0]) && realpath($argv[0]) === __FILE__) {
    $fine = false;

    while (!$fine) {
        print "SCEGLI L'OPERAZIONE DA FARE DUL DATABASE\n";
        print "Premere 1 per creare un record\n";
        print "Premere 2 per mostrare un record\n";
        print "Premere 3 per modificare un record\n";
        print "Premere 4 per eliminare un record\n";
        print "Premere 5 per mostrare tutti i record presenti nella tabella\n";
        print "Premere 6 per aggiungere oggetti alla tabella concerto da file JSON\n";
        print "Premere 7 per aggiungere oggetti alla tabella sale da file JSON\n";
        print "Premere 8 per risalire alla sala\n";
        print "Premere 9 per vedere i pezzi in una sala\n";
        print "Premere 0 per terminare il programma\n\n";

        $scelta = readline(); {
            switch ($scelta) {
                case "1": //CREAZIONE RECORD
                    do {
                        $codice = readline("Inserisci codice: ");
                    } while ($codice == null || !is_numeric($codice)); //inserimento codice 
                    do {
                        $titolo = readline("Inserisci titolo: ");
                    } while (trim($titolo) == "" || $titolo == null); //inserimento titolo
                    do {
                        $descrizione = readline("inserisci descrizione: ");
                    } while (trim($descrizione) == "" || $descrizione == null); //inserimento descrizione
                    do {
                        $dataString = readline("Inserisci data (Y-m-d): ");
                        $dataConcerto = DateTime::createFromFormat('Y-m-d', $dataString); //creo oggetto datetime dalla stringa inserita
                    } while ($dataString == null || !$dataConcerto); //inserimento data concerto 

                    $params = [
                        'codice' => (int)$codice,
                        'titolo' => $titolo,
                        'descrizione' => $descrizione,
                        'dataConcerto' => $dataConcerto,
                    ];

                    $concertoCreato = Concerto::Create($params);
                    if (!$concertoCreato) {
                        echo ("\nIl record non è stato creato..\n\n");
                        break;
                    }
                    echo ("\nRecord creato con successo.\n\n");
                    break;
                case "2": //mostra record
                    do {
                        $id = readline("Inserisci id del record da visualizzare: ");
                    } while ($id == null || !is_numeric($id)); //inserimento codice
                    $concertoDaVisualizzare = Concerto::Find((int) $id);
                    if (!$concertoDaVisualizzare) {
                        echo ("\nNon è stato trovato nessun record con id $id..\n\n");
                        break;
                    }
                    echo $concertoDaVisualizzare->ShowString();
                    break;
                case "3": // modifica record
                    do {
                        $id = readline("Inserisci id del record da modificare: ");
                    } while ($id == null || !is_numeric($id)); //inserimento codice
                    $concertoDaModificare = Concerto::Find((int) $id);
                    if (!$concertoDaModificare) {
                        echo ("\nNon è stato trovato nessun record con id $id..\n\n");
                        break;
                    }
                    do {
                        $codice = readline("Inserisci modifica codice: ");
                    } while (!is_numeric($codice) && $codice != null); //inserimento codice modificato, se non è null deve essere anche numerico
                    do {
                        $titolo = readline("Inserisci titolo: ");
                    } while (trim($titolo) == "" && $titolo != null); //inserimento titolo modificato, se non è null deve essere diverso da "" col metodo trim
                    do {
                        $descrizione = readline("inserisci descrizione: ");
                    } while (trim($descrizione) == "" && $descrizione != null); //inserimento descrizione modificato, se non è null deve essere diverso da "" col metodo trim
                    do {
                        $dataString = readline("Inserisci data (Y-m-d): ");
                        if ($dataString == null)
                            break;
                        $dataConcerto = DateTime::createFromFormat('Y-m-d', $dataString); //creo oggetto datetime dalla stringa inserita
                    } while (!$dataConcerto); //inserimento data concerto 

                    $params = [];
                    if ($codice != null) {
                        $params['codice'] = (int) $codice;
                    }
                    if ($titolo != null) {
                        $params['titolo'] = $titolo;
                    }
                    if ($descrizione != null) {
                        $params['descrizione'] = $descrizione;
                    }
                    if ($dataConcerto != null) {
                        $params['dataConcerto'] = $dataConcerto;
                    }

                    if (count($params) == 0) {
                        echo ("\nNon è stato modificato nessun campo del record con id $id.\n\n");
                        break;
                    }
                    $concertoModificato = $concertoDaModificare->Update($params);
                    if (!$concertoModificato) {
                        echo ("\nIl record non è stato modificato..\n\n");
                        break;
                    }
                    echo ("\nRecord modificato con successo.\n\n");
                    break;
                case "4": // elimina record 
                    do {
                        $id = readline("Inserisci id del record da eliminare: ");
                    } while ($id == null || !is_numeric($id)); //inserimento codice
                    $concertoDaEliminare = Concerto::Find((int) $id);
                    if (!$concertoDaEliminare) {
                        echo ("\nNon è stato trovato nessun record con id $id..\n\n");
                        break;
                    }
                    $concertoEliminato = $concertoDaEliminare->delete();
                    if (!$concertoEliminato) {
                        echo ("\nIl record non è stato eliminato..\n\n");
                        break;
                    }
                    echo ("\nRecord eliminato con successo.\n\n");
                    break;
                case "5": // mostra tutti record presenti
                    echo ("Elenco di tutti i record del database:\n");
                    $concerti = Concerto::FindAll();
                    if (!$concerti) {
                        echo ("\nNon sono presenti record nel database..\n\n");
                        break;
                    }

                    foreach ($concerti as $concerto) {
                        echo $concerto->ShowString();
                    }
                    break;
                case "6": //leggi da file JSON
                    $jsonFile = readline("Inserisci file json: ");
                    if (!str_contains($jsonFile, ".json"))
                        $jsonFile = $jsonFile . ".json";
                    $concerti = JsonReader::addFromJsonFile($jsonFile);
                    if (!$concerti) {
                        echo ("\nNon sono stati inseriti record nel database..\n\n");
                        break;
                    } else
                        echo ("\nRecord inseriti correttamente nel database.\n\n");
                    break;
                case "7": //leggi da file JSON
                    $jsonFile = readline("Inserisci file json: ");
                    if (!str_contains($jsonFile, ".json"))
                        $jsonFile = $jsonFile . ".json";
                    $concerti = JsonReader::addFromJsonFile($jsonFile);
                    if (!$concerti) {
                        echo ("\nNon sono stati inseriti record nel database..\n\n");
                        break;
                    } else
                        echo ("\nRecord inseriti correttamente nel database.\n\n");
                    break;
                case "8":
                    do {
                        $id = readline("Inserisci id del record da eliminare: ");
                    } while ($id == null || !is_numeric($id)); //inserimento codice
                    $SaladaRicercare = Concerto::Find((int) $id);
                    if (!$SaladaRicercare) {
                        echo ("\nNon è stato trovato nessun record con id $id..\n\n");
                        break;
                    }
                    $SalaRicercata = $SaladaRicercare->Sala();
                    break;

                case "9":
                    do {
                        $id = readline("Inserisci id del record da eliminare: ");
                    } while ($id == null || !is_numeric($id));
                    break; 
                case "0": //esci dal programma 
                    $fine = true;
                    DbManager::endConnection();
                    echo ("\nTerminazione del programma..\n\n");
                    break;
            }
        }
    }
}
