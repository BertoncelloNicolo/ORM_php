<?php

include 'ConnectionORM.php';

class Concerto
{
    private int $codice;
    private string $titolo;
    private string $descrizione;
    private DateTime $data;

    public function __construct(array $dati)
    {
        $this->__setCodice($dati['codice']);
        $this->__setTitolo($dati['titolo']);
        $this->__setDescrizione($dati['descrizione']);
        $this->__setData($dati['data']);
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
        return $this->data;
    }

    private function __setData(string $data)
    {
        $this->data = new DateTime($data);
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

    public function Show(): array
    {
        $filename = 'config.txt';
        $dbconnection = ConnectionManagement::ConnectToDB($filename);
        $values = ConnectionManagement::Select($filename,$dbconnection);
        ConnectionManagement::CloseConnection($dbconnection);
        return $values;
    }
}
