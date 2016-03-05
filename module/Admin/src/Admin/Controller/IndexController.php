<?php

namespace Admin\Controller;

use Core\Controller\CoreController;
use Zend\View\Model\ViewModel;

class IndexController extends CoreController
{
    public function indexAction()
    {
        return new ViewModel([
        ]);
    }
}
