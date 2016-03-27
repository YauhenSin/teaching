<?php

namespace Superadmin\Controller;

use Core\Controller\CoreController;
use Zend\View\Model\ViewModel;

class RequestsController extends CoreController
{
    public function indexAction()
    {
        $requests = $this->getRepository('Request')->findAll();
        return new ViewModel([
            'requests' => $requests,
        ]);
    }

    public function deleteAction()
    {
        /** @var \Core\Entity\Request $request */
        $request = $this->getEntity('Request', $this->params()->fromRoute('id'));
        if ($request) {
            $this->getEm()->remove($request);
            $this->getEm()->flush();
            $this->addFlashMessages(['Request has been deleted']);
            return $this->redirect()->toRoute('superadmin_requests_index', ['action' => 'index']);
        }
        return new ViewModel([
        ]);
    }
}
