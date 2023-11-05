<?php

include 'ConnectionORM.php';
include 'Concerto.php';

class Concerto
{
    private int $codice;
    private string $nome;
    private int $capienza;

    public function __construct(array $dati = null)
    {
        if ($dati != null) {
            $this->__setCodice($dati['codice']);
            $this->__setNome($dati['nome']);
            $this->__setCapienza($dati['capienza']);
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

    private function __getNome(): string
    {
        return $this->nome;
    }

    private function __setNome(string $nome)
    {
        $this->nome = $nome;
    }

    private function __getCapienza(): int
    {
        return $this->capienza;
    }

    private function __setCapienza(int $capienza)
    {
        $this->capienza = $capienza;
    }
}