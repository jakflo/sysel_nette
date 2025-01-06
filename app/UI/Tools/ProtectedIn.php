<?php
namespace App\UI\Tools;

// pomaha parametrizovat veci v IN vyrazu SQL dotazu
class ProtectedIn
{
    protected $prefixes = array();
    protected $data = array();
    
    /**
     * prida data pro 1 IN vyraz
     * @param string $prefix predpona tokenu pro IN vyraz
     * @param array $data prosty array
     * @throws \Exception
     */
    public function addArray(string $prefix, array $data) {
        if (count($data) == 0) {
            throw new \Exception('Pole pro IN nemuze byt prazdne');
        }
        
        if (!isset($this->prefixes[$prefix])) {
            $this->prefixes[$prefix] = 0;
            $c = 0;
        } else {
            $c = $this->prefixes[$prefix];
        }
        
        foreach ($data as $val) {
            $this->data["{$prefix}{$c}"] = $val;
            $c++;
            $this->prefixes[$prefix]++;            
        }
    }
    
    public function getTokens(string $prefix) {
        if (!isset($this->prefixes[$prefix])) {
            throw new \Exception("Prefix {$prefix} nenalezen");
        } else {
            $tokens = array();
            for ($c = 0; $c < $this->prefixes[$prefix]; $c++) {
                $tokens[] = ":{$prefix}{$c}";
            }
            return implode(',', $tokens);
        }
    }
    
    public function getData() {
        return $this->data;        
    }
}
