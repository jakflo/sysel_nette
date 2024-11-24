<?php

declare(strict_types=1);

namespace App\UI\Home;

use \App\UI\Entities\Warehouse;

final class HomePresenter extends \Nette\Application\UI\Presenter
{
    public function __construct(
            protected \Nette\Database\Explorer $dbe, 
            protected \App\UI\Model\EntityManagerFactory $db
    )
    {
        
    }
    
    public function renderDefault()
    {
        dump($this->dbe->query("SELECT * FROM warehouse")->fetchAll());
        $em = $this->db->create();
        $ws = $em->getRepository(Warehouse::class)->findAll();
        dump ($ws);
        
        $this->template->title = 'Syslovo sklad';
        
//        $this->template->reee = $this->dbe->query("SELECT * FROM warehouse");
    }
}
