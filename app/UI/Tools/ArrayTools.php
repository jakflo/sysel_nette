<?php
namespace App\UI\Tools;

use \Nette\Utils\ArrayHash;

class ArrayTools
{
    public static function searchInMultiArray(array $input, $value, string $search_in_key, bool $preserve_keys = false): array
    {
        $result = [];
        foreach ($input as $key => $row) {
            if ($row[$search_in_key] == $value) {
                if ($preserve_keys) {
                    $result[$key] = $row;                    
                }
                else {
                    $result[] = $row;
                }
            }
        }
        return $result;
    }
    
    public static function groupMultiArray(array $input, string $group_by_key, bool $preserve_keys = false): array
    {
        $result = [];
        $gruop_by_values = array_column($input, $group_by_key);
        foreach ($gruop_by_values as $item) {
            $result[$item] = self::searchInMultiArray($input, $item, $group_by_key, $preserve_keys);
        }
        return $result;
    }
    
    /**
     * prida prazdny placeholer k array urceny pro select ve formularich     
     * @return array
     */
    public static function addPlaceholderToArrayForSelect(array $input): array
    {
        return [null => '--------'] + $input;
    }
    
    /**
     * premeni vicepole na asociativni pole s pouze s parem klic - hodnota (ostatni sloupce jsou zahozeny)
     * @param array $input
     * @param string $key_name
     * @param string $value_name
     */
    public static function multiarrayToAsocPairs(array $input, string $key_name, string $value_name): array
    {
        $keys = array_column($input, $key_name);
        $values = array_column($input, $value_name);
        return array_combine($keys, $values);
    }
    
    // udela asociativni pole z prvich dvou sloupcu vicepole
    public static function asocPairsForFirstTwoInMultiarray(array $input): array
    {
        if (count($input) == 0) {
            return [];
        }
        
        $keys = array_keys($input[0]);
        if (count($keys) < 2) {
            throw new \Exception('Vysledek musi mit nejmene 2 sloupce');
        }
        
        return ArrayTools::multiarrayToAsocPairs($input, $keys[0], $keys[1]);
    }
    
    public static function multiarrayToArrayOfObjects(array $input): array
    {
        foreach ($input as &$row) {
            $row = ArrayHash::from($row, false);
        }
        return $input;
    }
    
}
