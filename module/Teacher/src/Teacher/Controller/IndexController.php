<?php

namespace Teacher\Controller;

use Core\Controller\CoreController;
use Zend\View\Model\ViewModel;

class IndexController extends CoreController
{
    public function indexAction()
    {
        $student = $this->getUser();
        $homework = $this->getRepository('Homework')->findOneBy([
            'group' => $student->getGroup(),
            'state' => \Core\Entity\Homework::STATE_ACTIVE,
        ]);
        return new ViewModel([
            'student' => $student,
            'homework' => $homework,
        ]);
    }
}
