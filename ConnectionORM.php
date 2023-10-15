<?php

class ConnectionManagement
{
    public static function ConnectToDB(bool $persistent = true): PDO
    {
        try {
            $dbconn = new PDO('mysql:host=localhost;dbname=prova', "root", "root", array(
                PDO::ATTR_PERSISTENT => $persistent
            )); //instaura una connessione con il database "concerti" su localhost, in base al valore di $persistent la connessione sarà persistente o no
            return $dbconn;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function CloseConnection(PDO $dbconn)
    {
        $dbconn = null;
    }

    public static function Insert(string $sql, PDO $dbconn): bool
    {
        try {
            $dbconn->query($sql); //prepara ed esegue la query
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
