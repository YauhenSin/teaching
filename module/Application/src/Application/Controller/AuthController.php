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

    public function registerAction()
    {
        // if the user is logged in, we don't need to register
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }
        $user = new User();
        $form = $this->createForm($user);
        $form->getInputFilter()->get('email')->getValidatorChain()->attach(new NoObjectExists([
            'object_repository' => $this->getRepository('User'),
            'fields' => [
                'email',
            ],
        ]));
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $userService = $this->getUserService();
                if ($userService->getOptions()->getEnableUserState()) {
                    $user->setState($userService->getOptions()->getDefaultUserState());
                }
                $bcrypt = new Bcrypt();
                $bcrypt->setCost($this->getOptions()->getPasswordCost());
                $user
                    ->setState(0)
                    ->setPassword($bcrypt->create($form->get('password')->getValue()))
                    ->addRole($this->getRepository('Role')->findOneBy(['roleId' => 'user']))
                    ->setActivationCode($user->getService()->generateActivationCode())
                ;
                                
                $this->getEm()->persist($user);
                $this->getEm()->flush();
                
                $this->getEventManager()->trigger('user_register_after', $user, [
                    'password' => $form->get('password')->getValue(),
                    'user' => $user,
                ]);

                return $this->redirect()->toRoute('zfcuser_success_register');
            }
        }
        return new ViewModel([
            'form' => $form,
        ]);
    }

    public function successRegisterAction()
    {
        return new ViewModel();
    }

    public function loginAction()
    {
        $request = $this->getRequest();
        if ($request->isGet()) {
            $userId = $this->params()->fromQuery('id');
            $activationCode = $this->params()->fromQuery('key');
            $user = $this->getEntity('User', $userId);
            if ($user && $user->getActivationCode() == $activationCode) {
                $user->setState(1);
                $this->getEm()->persist($user);
                $this->getEm()->flush();
            }
        }
        if ($request->isPost() && $request->getPost('remember')) {
            $this->getEventManager()->trigger('set_remember_me');
        }
        $result = parent::loginAction();
        if (!$request->isPost()) {
            return new ViewModel($result);
        }
        return $result;
    }
}