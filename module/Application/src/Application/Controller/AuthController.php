<?php

namespace Application\Controller;

use Core\Entity\User;
use DoctrineModule\Validator\NoObjectExists;
use Zend\Crypt\Password\Bcrypt;
use Zend\Form\Element;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use ZfcUser\Controller\UserController as ZFCUserController;
use Core\Traits\DoctrineBasicsTrait;
use Zend\Mvc\Exception;

class AuthController extends ZFCUserController
{
    use DoctrineBasicsTrait;

    /**
     * Execute the request
     *
     * @param  MvcEvent $e
     * @return mixed
     * @throws Exception\DomainException
     */
    public function onDispatch(MvcEvent $e)
    {
        parent::onDispatch($e);
        $this->getEventManager()->trigger('setLayout', $e);
    }

    public function loginAction()
    {
        $request = $this->getRequest();
        $result = parent::loginAction();
        if (!$request->isPost()) {
            return new ViewModel($result);
        }
        return $result;
    }
}