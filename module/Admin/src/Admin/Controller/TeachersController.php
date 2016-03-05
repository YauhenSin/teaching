<?php

namespace Admin\Controller;

use Core\Controller\CoreController;
use Core\Entity\User;
use Core\Validator\UniqueObject;
use DoctrineModule\Validator\NoObjectExists;
use Zend\Crypt\Password\Bcrypt;
use Zend\View\Model\ViewModel;

class TeachersController extends CoreController
{
    public function indexAction()
    {
        $teachers = $this->getRepository('User')->findByRole($this->getTeacherRole());
        return new ViewModel([
            'teachers' => $teachers,
        ]);
    }

    public function newAction()
    {
        $request = $this->getRequest();
        $user = new User();
        $form = $this->createForm($user);
        $form->getInputFilter()->get('email')->getValidatorChain()->attach(new NoObjectExists([
            'object_repository' => $this->getRepository('User'),
            'fields' => [
                'email',
            ],
        ]));
        $validationGroup = ['firstName', 'middleName', 'lastName', 'phone', 'additionalPhone', 'email', 'password'];
        $form->setValidationGroup($validationGroup);
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $user
                    ->addRole($this->getTeacherRole())
                    ->setPassword($this->encryptPassword($form->get('password')->getValue()))
                    ->setState(1)
                ;
                $this->getEm()->persist($user);
                $this->getEm()->flush();
                $this->addFlashMessages(['Teacher has been created']);
                return $this->redirect()->toRoute('admin_teachers_index', ['action' => 'index']);
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
            return $this->redirect()->toRoute('admin_teachers_index', ['action' => 'index']);
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
        $validationGroup = ['firstName', 'middleName', 'lastName', 'phone', 'additionalPhone', 'email'];
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
                $this->addFlashMessages(['Teacher has been saved']);
                return $this->redirect()->toRoute('admin_teachers_index', ['action' => 'index']);
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
    protected function getTeacherRole()
    {
        return $this->getRepository('Role')->findOneBy(['roleId' => 'teacher']);
    }
}
