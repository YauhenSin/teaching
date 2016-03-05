<?php

namespace Superadmin\Controller;

use Core\Controller\CoreController;
use Core\Entity\User;
use Zend\View\Model\ViewModel;

class AdminController extends CoreController
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
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $user
                    ->addRole($this->getAdminRole())
                    ->setState(1)
                ;
                $this->getEm()->persist($user);
                $this->getEm()->flush();
                $this->addFlashMessages(['Admin has been created']);
                return $this->redirect()->toRoute('superadmin_admin_index', ['action' => 'index']);
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
            return $this->redirect()->toRoute('superadmin_admin_index', ['action' => 'index']);
        }
        $form = $this->createForm($user);
        $form->setValidationGroup([
            'firstName', 'lastName', 'email'
        ]);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getEm()->persist($user);
                $this->getEm()->flush();
                $this->addFlashMessages(['Admin has been saved']);
                return $this->redirect()->toRoute('superadmin_admin_index', ['action' => 'index']);
            }
        }
        return new ViewModel([
            'form' => $form,
        ]);
    }

    /**
     * @return \Core\Entity\Role
     */
    protected function getAdminRole()
    {
        return $this->getEntity('Role', 4);
    }
}
