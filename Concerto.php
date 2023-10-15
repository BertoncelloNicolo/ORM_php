<?php

include 'ConnectionORM.php';

class Concerto
{
    private static int $id = 0;
    private int $codice;
    private string $titolo;
    private string $descrizione;
    private DateTime $data;

    public function __construct(array $dati)
    {
        self::$id++;
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

    private function __setData(DateTime $data)
    {
        $this->data = $data;
    }

    static public function Create(array $dati): Concerto
    {
        try {
            $dbconnection = ConnectionManagement::ConnectToDB();
            $sql = "INSERT INTO prova.concerti (codice, titolo, descrizione, data) VALUES (
                " . $dati['codice'] . ",
                '" . $dati['titolo'] . "',
                '" . $dati['descrizione'] . "',
                '" . $dati['data']->format('Y-m-d H:i:s') . "'
            )";
            ConnectionManagement::Insert($sql, $dbconnection);
            ConnectionManagement::CloseConnection($dbconnection);
            return new Concerto($dati);
        } catch (Exception $e) {
            return null;
        }
    }

    public function Show(): array
    {
        return $dati = [
            'codice' => $this->__getCodice(),
            'titolo' => $this->__getTitolo(),
            'descrizione' => $this->__getDescrizione(),
            'data' => $this->__getData(),
        ];
    }
}
