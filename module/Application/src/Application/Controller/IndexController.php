<?php

namespace Application\Controller;

use Core\Controller\CoreController;
use Core\Entity\Request;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends CoreController
{
    public function indexAction()
    {
        $this->layout('layout/main');
        return new ViewModel();
    }

    public function requestAction()
    {
        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('home');
        }
        if ($request->isGet()) {
            return new JsonModel([
                'success' => false,
            ]);
        }
        $requestEntity = new Request();
        $form = $this->createForm($requestEntity);
        $form->setData($request->getPost());
        if ($form->isValid()) {
            $this->getEm()->persist($requestEntity);
            $this->getEm()->flush();
            return new JsonModel([
                'success' => true,
                'message' => 'Request has been sent',
            ]);
        }
        return new JsonModel([
            'success' => false,
        ]);
    }
}
