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
        $data = $dati['data_concerto'];
        try {
            $sql = "INSERT INTO $dbname.$tbname (codice, titolo, descrizione, data_concerto) VALUES (:codice, :titolo, :descrizione, :data_concerto)";
            $stmt = $dbconnection->prepare($sql);

            $stmt->bindParam(':codice', $dati['codice']);
            $stmt->bindParam(':titolo', $dati['titolo']);
            $stmt->bindParam(':descrizione', $dati['descrizione']);
            if ($data instanceof DateTime) {
                $dataformatted = $data->format('Y-m-d H:i:s');
                $stmt->bindParam(':data_concerto', $dataformatted);
            } else
                $stmt->bindParam(':data_concerto', $data);



            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function Create(array $campi, string $filename, PDO $dbconnection): bool
    {
        $config = ConnectionManagement::ObtainConfig($filename);
        $dbname = $config['dbname'];
        $tbname = $config['table'];
        $host = $config['host'];
        $user = $config['user'];
        $password = $config['password'];
        try {
            $sql = "CREATE DATABASE IF NOT EXISTS $dbname;
            CREATE TABLE IF NOT EXISTS $dbname.$tbname(
            id int not null auto_increment primary key,
            {$campi[0]} int,
            {$campi[1]} varchar(50),
            {$campi[2]} varchar(50),
            {$campi[3]} DateTime
            );";
            $dbconnection->query($sql);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function Select(string $filename, PDO $dbconnection): Concerto
    {
        $config = ConnectionManagement::ObtainConfig($filename);
        $dbname = $config['dbname'];
        $tbname = $config['table'];
        $id = $dbconnection->lastInsertId();
        $sql = $dbconnection->prepare("select * from $dbname.$tbname where id=:id");
        $sql->bindParam(":id", $id);
        $sql->execute();
        return $sql->fetchObject('Concerto');
    }

    private static function ObtainConfig(string $filename): array
    {
        $config = file_get_contents($filename); //legge file
        $configLines = explode("\n", $config); //lo divide in base a \n
        $connectionInfo = [];
        foreach ($configLines as $line) {
            list($key, $value) = explode('=', $line); //ogni riga diventa chiave=valore
            $connectionInfo[trim($key)] = trim($value);
        }
        return $connectionInfo;
    }
}
