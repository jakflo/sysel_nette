<?php
namespace App\UI\Model;

use \Nette\Database\ResultSet;

class Database extends \Nette\Database\Explorer
{    
    /**
     * prepise dotaz s pouzitim jmennych parametru na dotaz s pozicnimi parametry a odesle do Database\Explorer
     * @param string $sql
     * @param array $parameters - obsahuje jmenne parametry vc. dvojtecky
     * @return ResultSet
     */
    public function queryWithNamedParameters(string $sql, array $parameters = []): ResultSet
    {
        $positionalParameters = [];
        $convertedQuery = preg_replace_callback(
            '/:(\w+)/', 
            function ($matches) use (&$positionalParameters, $parameters) {
                $paramName = $matches[0];
                if (!array_key_exists($paramName, $parameters)) {
                    throw new \Exception("Missing parameter: $paramName");
                }                
                $positionalParameters[] = $parameters[$paramName];
                return '?';
            },
            $sql
        );
            
        return parent::query($convertedQuery, ...$positionalParameters);
    }
    
    
    
    
}
