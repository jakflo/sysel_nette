<?php
namespace App\UI\ItemsLotList;

use \App\UI\TableFilters\TableFilterCollection;
use \App\UI\TableFilters\TableFilterText;

class ItemsLotListFilers extends TableFilterCollection
{
    public function setFilters()
    {
        $this->addFilter(new TableFilterText('lot_name', 'Šarže', 'il.lot', true));
    }
}
