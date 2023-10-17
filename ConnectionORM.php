<?php

class ConnectionManagement
{
    public static function ConnectToDB(string $filename, bool $persistent = true): PDO
    {
        $config = ConnectionManagement::ObtainConfig($filename);
        $host = $config['host'];
        $dbname = $config['dbname'];
        $user = $config['user'];
        $password = $config['password'];
        try {
            $dbconnection = new PDO("mysql:host=$host;dbname=$dbname", $user, $password, array(
                PDO::ATTR_PERSISTENT => $persistent
            )); //instaura una connessione con il database "concerti" su localhost, in base al valore di $persistent la connessione sarà persistente o no
            return $dbconnection;
        } catch (Exception $e) {
            return null;
        }
    }
    public static function ConnectToHost(string $filename, bool $persistent = true): PDO
    {
        $config = ConnectionManagement::ObtainConfig($filename);
        $host = $config['host'];
        $user = $config['user'];
        $password = $config['password'];
        try {
            $dbconnection = new PDO("mysql:host=$host", $user, $password, array(
                PDO::ATTR_PERSISTENT => $persistent
            )); //instaura una connessione con il database, in base al valore di $persistent la connessione sarà persistente o no
            return $dbconnection;
        } catch (Exception $e) {
            return null;
        }
    }
    public static function CloseConnection(PDO $dbconnection)
    {
        $dbconnection = null;
    }

    public static function Insert(array $dati, string $filename, PDO $dbconnection): bool
    {
        $config = ConnectionManagement::ObtainConfig($filename);
        $dbname = $config['dbname'];
        $tbname = $config['table'];
        try {
            $sql = "INSERT INTO $dbname.$tbname (codice, titolo, descrizione, data) VALUES (:codice, :titolo, :descrizione, :data)";
            $stmt = $dbconnection->prepare($sql);

            $stmt->bindParam(':codice', $dati['codice']);
            $stmt->bindParam(':titolo', $dati['titolo']);
            $stmt->bindParam(':descrizione', $dati['descrizione']);
            $stmt->bindParam(':data', $dati['data']);


            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function Create(string $filename, array $campi, PDO $dbconnection): bool
    {
        $config = ConnectionManagement::ObtainConfig($filename);
        $dbname = $config['dbname'];
        $tbname = $config['table'];
        $host = $config['host'];
        $user = $config['user'];
        try {
            $sql = "CREATE DATABASE IF NOT EXISTS $dbname;
            CREATE TABLE IF NOT EXISTS $dbname.$tbname(
            id int not null auto_increment primary key,
            {$campi[0]} int,
            {$campi[1]} varchar(50),
            {$campi[2]} varchar(50),
            {$campi[3]} varchar(50)
            );
            GRANT Create ON $dbname.* TO $user@$host;
            GRANT Alter ON $dbname.* TO $user@$host;
            GRANT Insert ON $dbname.* TO $user@$host;
            GRANT Select ON $dbname.* TO $user@$host;";
            $dbconnection->query($sql);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function Select(string $filename, PDO $dbconnection): array
    {
        $config = ConnectionManagement::ObtainConfig($filename);
        $dbname = $config['dbname'];
        $tbname = $config['table'];
        $id = $dbconnection->lastInsertId();
        $sql = "SELECT * FROM $dbname.$tbname WHERE id = $id";
        $stmt = $dbconnection->query($sql);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        $codice = $record['codice'];
        $titolo = $record['titolo'];
        $descrizione = $record['descrizione'];
        $data = $record['data'];
        return ['codice' => $codice, 'titolo' => $titolo, 'descrizione' => $descrizione, 'data' => $data];
    }

    private static function ObtainConfig(string $nome_file): array
    {
        $config = file_get_contents($nome_file); //legge file
        $configLines = explode("\n", $config); //lo divide in base a \n
        $connectionInfo = [];
        foreach ($configLines as $line) {
            list($key, $value) = explode('=', $line); //ogni riga diventa chiave=valore
            $connectionInfo[trim($key)] = trim($value);
        }
        return $connectionInfo;
    }
}
