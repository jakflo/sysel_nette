<?php

declare(strict_types=1);

namespace App\UI\Home;

use \App\UI\Entities\Warehouse;

final class HomePresenter extends \Nette\Application\UI\Presenter
{
    public function renderDefault()
    {
        $this->template->title = 'Syslovo sklad';
    }
}
