<?php

namespace User\Controller;

use Core\Controller\CoreController;
use DoctrineModule\Validator\NoObjectExists;
use Zend\View\Model\ViewModel;

class ProfileController extends CoreController
{
    public function indexAction()
    {
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            if ($this->isAdminUser()) {
                return $this->redirect()->toRoute('admin_index_index', ['action' => 'index']);
            }
            return $this->redirect()->toRoute('user_account_settings', ['action' => 'edit-profile']);
        }
        return $this->redirect()->toRoute('zfcuser/login');
    }

    public function twitterAction()
    {
        $user = $this->getUser()->setEmail('');
        $form = $this->createForm($user)->setValidationGroup('email');
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
                $this->getEm()->persist($user);
                $this->getEm()->flush();
                $this->getEventManager()->trigger("mailing_registration", $this, ['user' => $user]);
                return $this->redirect()->toRoute('user_profile_index');
            }
        }
        return new ViewModel([
            'form' => $form,
        ]);
    }
}
