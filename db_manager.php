<?php

class DbManager
{
    private $configurationFile = 'config.txt';
    private $sqlFile = 'organizzazione_concerti.sql';
    private $connectionInfo;
    private $sqlInstructions;
    private $connection;
    private static $instance = null;

    private function __construct()
    {
        $this->__setConfig();
        $this->__setSqlInstructions();
        $this->__setConnection(true);
    }

    //restituisce l'istanza di DbManager, nel caso non esista la crea
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DbManager();
        }
        return self::$instance;
    }

    public static function endConnection()
    {
        if (self::$instance != null) {
            self::$instance->CloseConnection();
            return true;
        }
        return false;
    }

    private function __getConfigurationFile(): string
    {
        return $this->configurationFile;
    }

    private function __getSqlFile(): string
    {
        return $this->sqlFile;
    }

    private function __setConfig()
    {
        $this->connectionInfo = $this->ObtainConnectionInfo($this->__getConfigurationFile());
    }

    private function __setSqlInstructions()
    {
        $this->sqlInstructions = $this->ObtainSqlInstructions($this->__getSqlFile());
    }

    private function __setConnection(bool $persistent)
    {
        $this->connection = $this->ConnectToDB($persistent);
        if (!$this->connection) {
            echo ("\nErrore: non è stato possibile stabilire una connessione col database..\n");
            exit();
        }
    }

    //connessione a database
    private function ConnectToDB($persistent): PDO | null
    {
        try {
            $dbConnection = new PDO("mysql:host={$this->connectionInfo['host']};dbname={$this->connectionInfo['database']}", $this->connectionInfo['user'], $this->connectionInfo['password'], array(
                PDO::ATTR_PERSISTENT => $persistent
            )); //instaura una connessione con il database, in base al valore di $persistent la connessione sarà persistente o no
            $this->CreateTable($dbConnection);
            return $dbConnection;
        } catch (PDOException $e) {
            //se il codice d'errore è 1049 (unknown database), prova a crearlo
            if ($e->getCode() == 1049) {
                $hostConnection = $this->ConnectToHost();
                if ($this->CreateDatabase($hostConnection)) //nel caso riesca a crearlo riprova a restaurare una connessione
                    return $this->ConnectToDB($persistent);
            }
            return null;
        }
    }

    //connessione a host, utente
    private function ConnectToHost(): PDO | null
    {
        try {
            $hostConnection = new PDO("mysql:host={$this->connectionInfo['host']}",  $this->connectionInfo['user'], $this->connectionInfo['password']);
            return $hostConnection;
        } catch (Exception $e) {
            return null;
        }
    }

    //chiusura della connessione
    public function CloseConnection()
    {
        $this->connection = null;
    }

    //ritorna la configurazione all'interno del file contenente le informazioni di connessione
    private function ObtainConnectionInfo($configFile): array
    {
        $config = file_get_contents($configFile); //legge file
        $configLines = explode("\n", $config); //lo divide in base a \n
        $connectionInfo = [];
        foreach ($configLines as $line) {
            list($key, $value) = explode('=', $line); //ogni riga diventa chiave=valore
            $connectionInfo[trim($key)] = trim($value);
        }
        return $connectionInfo;
    }

    //ritorna le istruzioni contenute nel file sql
    private function ObtainSqlInstructions($sqlFile): array
    {
        $instructions = file_get_contents($sqlFile); //legge file
        $singleInstructions = explode("--", $instructions); //lo divide in base a --
        $keyValueInstructions = [];
        foreach ($singleInstructions as $instruction) {
            $lines = explode("\n", $instruction);
            $title = trim(array_shift($lines)); // la prima riga è il titolo dell'istruzione
            $sql = implode("\n", $lines); // le altre righe sono l'istruzione
            if ($sql != "") {
                $sql = str_replace(":dbname", $this->connectionInfo['database'], $sql); //rimpiazza :dbname con il nome del database all'interno della stringa sql
                $sql = str_replace(":tbname", $this->connectionInfo['table'], $sql); //rimpiazza :tbname con il nome della tabella all'interno della stringa sql
                $keyValueInstructions[trim($title)] = trim($sql);
            }
        }

        return $keyValueInstructions;
    }


    //creazione database
    private function CreateDatabase($hostConnection): bool
    {
        try {
            $sql = $this->sqlInstructions['create_database'];
            if (!$hostConnection->query($sql))
                return false;
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    //crezione tabella con relativi campi
    private function CreateTable($dbConnection): bool
    {
        try {
            $sql = $this->sqlInstructions['create_table'];
            if (!$dbConnection->query($sql))
                return false;
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    //Creazione record con valori forniti da un parametro array
    public static function Insert($params): Concerto | null
    {
        $dbConnection = DbManager::getInstance()->connection;
        $sqlInstructions = DbManager::getInstance()->sqlInstructions;

        try {
            $sql = $sqlInstructions['insert_record'];

            $stmt = $dbConnection->prepare($sql);
            $stmt->bindParam(':codice', $params['codice']);
            $stmt->bindParam(':titolo', $params['titolo']);
            $stmt->bindParam(':descrizione', $params['descrizione']);
            if ($params['dataConcerto'] instanceof DateTime) //se il valore di dataConcerto è istanza di DateTime allora viene formattata a stringa
                $dataConcerto = $params['dataConcerto']->format('Y-m-d H:i:s');
            else
                $dataConcerto = $params['dataConcerto'];
            $stmt->bindParam(':dataConcerto', $dataConcerto);
            if (!$stmt->execute())
                return null;
            return DbManager::Select($dbConnection->lastInsertId());
        } catch (Exception $e) {
            return null;
        }
    }

    //seleziona un record tramite id fornito in input
    public static function Select($id): Concerto | null | false
    {
        $dbConnection = DbManager::getInstance()->connection;
        $sqlInstructions = DbManager::getInstance()->sqlInstructions;

        try {

            $sql = $sqlInstructions['select_id'];
            $stmt = $dbConnection->prepare($sql);
            $stmt->bindParam(":id", $id);
            if (!$stmt->execute())
                return null;
            if (!$concerto = $stmt->fetchObject('Concerto'))
                throw new Exception();
            return $concerto;
        } catch (Exception $e) {
            return null;
        }
    }
    //seleziona tutti i record di una tabella
    public static function SelectAll(): array | null
    {
        $dbConnection = DbManager::getInstance()->connection;
        $sqlInstructions = DbManager::getInstance()->sqlInstructions;

        try {
            $sql = $sqlInstructions['select_all'];
            $stmt = $dbConnection->prepare($sql);
            if (!$stmt->execute())
                return null;
            return $stmt->fetchAll(PDO::FETCH_CLASS, 'Concerto');
        } catch (Exception $e) {
            return null;
        }
    }

    //elimina un record tramite id fornito in input
    public static function Delete($id): Concerto | null
    {
        $dbConnection = DbManager::getInstance()->connection;
        $sqlInstructions = DbManager::getInstance()->sqlInstructions;

        try {
            $deletedObj = DbManager::Select($id);
            $sql = $sqlInstructions['delete_id'];
            $stmt = $dbConnection->prepare($sql);
            $stmt->bindParam(":id", $id);
            if (!$stmt->execute())
                return null;
            return $deletedObj;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function Update($params, $id): Concerto | null
    {
        $dbConnection = DbManager::getInstance()->connection;
        $sqlInstructions = DbManager::getInstance()->sqlInstructions;

        try {
            $sql = "";
            if (isset($params['codice'])) //controlla se la chiave è settata
                $sql = $sql . $sqlInstructions['update_codice'];
            if (isset($params['titolo']))
                $sql = $sql . $sqlInstructions['update_titolo'];
            if (isset($params['descrizione']))
                $sql = $sql . $sqlInstructions['update_descrizione'];
            if (isset($params['dataConcerto']))
                $sql = $sql . $sqlInstructions['update_data'];
            $sql = $sql . $sqlInstructions['select_id'];
            $stmt = $dbConnection->prepare($sql);
            if (str_contains($sql, ":codice"))
                $stmt->bindParam(':codice', $params['codice']);
            if (str_contains($sql, ":titolo"))
                $stmt->bindParam(':titolo', $params['titolo']);
            if (str_contains($sql, ":descrizione"))
                $stmt->bindParam(':descrizione', $params['descrizione']);
            if (str_contains($sql, ":dataConcerto")) {
                $dataConcerto = $params['dataConcerto']->format('Y-m-d H:i:s');
                $stmt->bindParam(':dataConcerto', $dataConcerto);
            }
            $stmt->bindParam(':id', $id);
            if (!$stmt->execute())
                return null;
            //attesa prima della fine della query
            $stmt->closeCursor();
            return DbManager::Select($id);
        } catch (Exception $e) {
            return null;
        }
    }

    public static function sala($id) : Concerto | null
    {
        $dbConnection = DbManager::getInstance()->connection;
        $sqlInstructions = DbManager::getInstance()->sqlInstructions;

        try {
            $sala = DbManager::Select($id); // usiamo la select per andare a chiamare l'oggetto che vogliamo 
            $sql = $sqlInstructions['select_sala_concerti'];
            $stmt = $dbConnection->prepare($sql);
            $stmt->bindParam(":id", $id);
            if (!$stmt->execute())
                return null;
            return $sala;
        } catch (Exception $e) {
            return null;
        }
    }
}
