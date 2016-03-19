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
        $administrators = $this->getRepository('User')->findByRoleAndOwner($this->getAdminRole(), $this->getUser());
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
                    ->setOwner($this->getUser())
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

    public function blockAction()
    {
        /** @var \Core\Entity\User $user */
        $user = $this->getEntity('User', $this->params()->fromRoute('id'));
        if ($user) {
            try {
                $user
                    ->setState(User::STATE_BLOCKED_BY_SUPERADMIN)
                    ->getService()->addStateToStack(User::STATE_BLOCKED_BY_SUPERADMIN);
                /** @var \Core\Entity\User [] $relatedUsers */
                $relatedUsers = $this->getRepository('User')->findBy(['owner' => $user]);
                foreach ($relatedUsers as $relatedUser) {
                    $relatedUser
                        ->setState(User::STATE_IS_ADMIN_BLOCKED)
                        ->getService()->addStateToStack(User::STATE_IS_ADMIN_BLOCKED);
                    $this->getEm()->persist($relatedUser);
                }
                $this->getEm()->persist($user);
                $this->getEm()->flush();
                $this->addFlashMessages(['Admin has been blocked']);
            } catch(\Exception $exception) {
                $this->addFlashMessages(['Something wrong. Try again.'], 'error');
            }
            return $this->redirect()->toRoute('superadmin_admins_index', ['action' => 'index']);
        }
        return new ViewModel([
        ]);
    }

    public function activateAction()
    {
        /** @var \Core\Entity\User $user */
        $user = $this->getEntity('User', $this->params()->fromRoute('id'));
        if ($user) {
            try {
                $user
                    ->setState(User::STATE_ACTIVE)
                    ->getService()->removeStateFromStack(User::STATE_BLOCKED_BY_SUPERADMIN);
                /** @var \Core\Entity\User [] $relatedUsers */
                $relatedUsers = $this->getRepository('User')->findBy(['owner' => $user]);
                foreach ($relatedUsers as $relatedUser) {
                    $relatedUser->getService()->removeStateFromStack(User::STATE_IS_ADMIN_BLOCKED);
                    if (!$relatedUser->getStatesStack()) {
                        $relatedUser->setState(User::STATE_ACTIVE);
                    }
                    $this->getEm()->persist($relatedUser);
                }
                $this->getEm()->persist($user);
                $this->getEm()->flush();
                $this->addFlashMessages(['Admin has been activated']);
            } catch(\Exception $exception) {
                $this->addFlashMessages(['Something wrong. Try again.'], 'error');
            }
            return $this->redirect()->toRoute('superadmin_admins_index', ['action' => 'index']);
        }
        return new ViewModel([
        ]);
    }

    public function deleteAction()
    {
        /** @var \Core\Entity\User $user */
        $user = $this->getEntity('User', $this->params()->fromRoute('id'));
        if ($user) {
            try {
                $this->getEm()->remove($user);
                $this->getEm()->flush();
                $this->addFlashMessages(['Admin has been deleted']);
            } catch(\Exception $exception) {
                $this->addFlashMessages(['Need to remove all related teachers, students etc.'], 'error');
            }
            return $this->redirect()->toRoute('superadmin_admins_index', ['action' => 'index']);
        }
        return new ViewModel([
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
