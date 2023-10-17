<?php

include 'ConnectionORM.php';

class Concerto
{
    private int $codice;
    private string $titolo;
    private string $descrizione;
    private $data_concerto;

    public function __construct(array $dati = null)
    {
        if ($dati != null) {
            $this->__setCodice($dati['codice']);
            $this->__setTitolo($dati['titolo']);
            $this->__setDescrizione($dati['descrizione']);
            $this->__setData($dati['data_concerto']);
        }
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

    private function __getData(): DateTime
    {
        if ($this->data_concerto instanceof DateTime)
            return $this->data_concerto;
        else
            return new DateTime($this->data_concerto);
    }

    private function __setData($data_concerto)
    {
        if ($data_concerto instanceof DateTime)
            $this->data_concerto = $data_concerto;
        else
            $this->data_concerto = new DateTime($data_concerto);
    }

    static public function Create(array $dati, string $filename): Concerto
    {
        try {
            $dbconnection = ConnectionManagement::ConnectToDB($filename);
            ConnectionManagement::Insert($dati, $filename, $dbconnection);
            ConnectionManagement::CloseConnection($dbconnection);
            return new Concerto($dati);
        } catch (Exception $e) {
            return null;
        }
    }

    public function Show(string $filename): Concerto
    {
        $dbconnection = ConnectionManagement::ConnectToDB($filename);
        $obj_found = ConnectionManagement::Select($filename, $dbconnection);
        ConnectionManagement::CloseConnection($dbconnection);
        return $obj_found;
    }

    public static function ToArray(Concerto $object): array
    {
        $array = array();
        foreach ($object as $key => $value) {
            $array[$key] = $value;
        }
        return $array;
    }
}
