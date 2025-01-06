<?php
namespace App\UI\Model;

use \Doctrine\DBAL\Result;
use \App\UI\Tools\ArrayTools;

// wraper pro objekt \Doctrine\DBAL\Connection
class Database
{
    protected \Doctrine\DBAL\Connection $db;

    public function __construct(
            protected \Doctrine\ORM\EntityManager $em
    )
    {
        $this->db = $em->getConnection();
    }
    
    public function query(string $sql, array $params = []): Result
    {
        return $this->db->executeQuery($sql, $params);
    }
    
    // $types - nuti typ parametru, assoc. pole ve tvaru 'jmeno_sloupce' => objekt napr. \Doctrine\DBAL\Types\Type::getType('integer')
    public function fetchAllNumeric(string $query, array $params = [], array $types = []): array
    {
        return $this->db->fetchAllNumeric($query, $params, $types);
    }

    public function fetchAllAssociative(string $query, array $params = [], array $types = []): array
    {
        return $this->db->fetchAllAssociative($query, $params, $types);
    }
    
    /**     
     * @return array ArrayHash objekt
     */
    public function fetchAllObjects(string $query, array $params = [], array $types = []): array
    {
        $rows = $this->db->fetchAllAssociative($query, $params, $types);
        return ArrayTools::multiarrayToArrayOfObjects($rows);
    }

    public function fetchAllKeyValue(string $query, array $params = [], array $types = []): array
    {
        return $this->db->fetchAllKeyValue($query, $params, $types);
    }

    public function fetchAllAssociativeIndexed(string $query, array $params = [], array $types = []): array
    {
        return $this->db->fetchAllAssociativeIndexed($query, $params, $types);
    }

    public function fetchFirstColumn(string $query, array $params = [], array $types = []): array
    {
        return $this->db->fetchFirstColumn($query, $params, $types);
    }
    
    public function fetchOneField(string $query, array $params = [], array $types = [])
    {
        return $this->db->fetchOne($query, $params, $types);
    }
    
    public function fetchPairs(string $query, array $params = [], array $types = []): array
    {
        $rows = $this->db->fetchAllAssociative($query, $params, $types);
        if (count($rows) == 0) {
            return [];
        }
        
        $keys = array_keys($rows[0]);
        if (count($keys) < 2) {
            throw new \Exception('Vysledek musi mit nejmene 2 sloupce');
        }
        
        return ArrayTools::multiarrayToAsocPairs($rows, $keys[0], $keys[1]);
    }
    
    public function executeQuery(
        string $sql,
        array $params = [],
        $types = [],
        ?QueryCacheProfile $qcp = null
    ): Result {
        return $this->db->executeQuery($sql, $params, $types, $qcp);
    }
    
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }
    
    public function commit()
    {
        return $this->db->commit();
    }
    
    public function rollBack()
    {
        return $this->db->rollBack();
    }
    
}
