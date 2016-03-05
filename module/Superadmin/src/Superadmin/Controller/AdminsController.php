<?php

namespace Superadmin\Controller;

use Core\Controller\CoreController;
use Core\Entity\User;
use DoctrineModule\Validator\NoObjectExists;
use Zend\Crypt\Password\Bcrypt;
use Zend\View\Model\ViewModel;
use Core\Validator\UniqueObject;

class AdminsController extends CoreController
{
    public function indexAction()
    {
        $administrators = $this->getRepository('User')->findByRole($this->getAdminRole());
        return new ViewModel([
            'administrators' => $administrators,
        ]);
    }

    public function newAction()
    {
        $user = new User();
        $form = $this->createForm($user);
        $form->setValidationGroup([
            'firstName', 'lastName', 'email', 'password'
        ]);
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
                $user
                    ->addRole($this->getAdminRole())
                    ->setPassword($this->encryptPassword($form->get('password')->getValue()))
                    ->setState(1)
                ;
                $this->getEm()->persist($user);
                $this->getEm()->flush();
                $this->addFlashMessages(['Admin has been created']);
                return $this->redirect()->toRoute('superadmin_admins_index', ['action' => 'index']);
            }
        }
        return new ViewModel([
            'form' => $form,
        ]);
    }

    public function editAction()
    {
        /** @var \Core\Entity\User $user */
        $user = $this->getRepository('User')->findOneBy(['id' => $this->params()->fromRoute('id')]);
        if (!$user) {
            return $this->redirect()->toRoute('superadmin_admins_index', ['action' => 'index']);
        }
        $request = $this->getRequest();
        $form = $this->createForm($user);
        $form->getInputFilter()->get('email')->getValidatorChain()->attach(new UniqueObject([
            'object_repository' => $this->getRepository('User'),
            'id' => $user->getId(),
            'fields' => [
                'email',
            ],
        ]));
        $validationGroup = ['firstName', 'lastName', 'email'];
        if ($request->getPost()->get('password')) {
            array_push($validationGroup, 'password');
        }
        $form->setValidationGroup($validationGroup);
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $password = $form->get('password')->getValue();
                if ($password) {
                    $user->setPassword($this->encryptPassword($password));
                }
                $this->getEm()->persist($user);
                $this->getEm()->flush();
                $this->addFlashMessages(['Admin has been saved']);
                return $this->redirect()->toRoute('superadmin_admins_index', ['action' => 'index']);
            }
        }
        return new ViewModel([
            'form' => $form,
        ]);
    }

    /**
     * @param string $password
     * @return string
     */
    protected function encryptPassword($password)
    {
        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->getZfcUserModuleOptions()->getPasswordCost());
        return $bcrypt->create($password);
    }

    /**
     * @return \ZfcUser\Options\ModuleOptions
     */
    protected function getZfcUserModuleOptions()
    {
        return $this->getServiceLocator()->get('zfcuser_module_options');
    }

    /**
     * @return \Core\Entity\Role
     */
    protected function getAdminRole()
    {
        return $this->getRepository('Role')->findOneBy(['roleId' => 'admin']);
    }
}
