<?php

class JsonReader
{
    public static function addFromJsonFile($jsonFile): array | null
    {
        $concertiArray = [];
        try {
            if(!file_exists($jsonFile))
                return null;
            $jsonContents = file_get_contents($jsonFile); //legge contenuto del file json
            $jsonObjects = json_decode($jsonContents, true); //decodifica il file json
            if (!is_array($jsonObjects)) //nel caso non venga decodificato nel modo corretto
                return null;

            foreach ($jsonObjects as $object) {
                if (!isset($object['codice'], $object['titolo'], $object['descrizione'], $object['dataConcerto']))
                    continue;
                $concertiArray[] = Concerto::Create($object);
            }
            return $concertiArray;
        } catch (Exception $e) {
            return null;
        }
    }
}
