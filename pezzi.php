<?php

require_once 'db_manager.php';
require_once 'json_reader.php';
class Pezzi
{

    private int $codice;
    private string $titolo;

    public function __construct($codice, string $titolo)
    {
        if (strlen($codice) == 0 && strlen($titolo) == 0) {
            $this->codice = $codice;
            $this->titolo = $titolo;
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
        return $this->titolo;
    }

    private function __setNome(string $titolo)
    {
        $this->titolo = $titolo;
    }


    public static function Find(int $id): Concerto | null
    {
        try {
            return DbManager::Select($id);
        } catch (Exception $e) {
            return null;
        }
    }

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
}
