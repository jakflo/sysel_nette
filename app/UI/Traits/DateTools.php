<?php
namespace App\UI\Traits;

trait DateTools
{
    public function reformatEnDate(string $date_en_format, string $format): string
    {
        $date = new \DateTime($date_en_format);
        return $date->format($format);
    }
    
    public function formatEnDateToCzFormat(string $date_en_format): string
    {        
        return $this->reformatEnDate($date_en_format, 'd. m. Y');
    }
    
    public function formatEnDateTimeToCzFormat(string $date_en_format): string
    {        
        return $this->reformatEnDate($date_en_format, 'd. m. Y H:i:s');
    }
    
}
