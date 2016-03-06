<?php

namespace Admin\Controller;

use Core\Controller\CoreController;
use Core\Entity\User;
use Core\Validator\UniqueObject;
use DoctrineModule\Validator\NoObjectExists;
use Zend\Crypt\Password\Bcrypt;
use Zend\View\Model\ViewModel;

class StudentsController extends CoreController
{
    public function indexAction()
    {
        $students = $this->getRepository('User')->findByRoleAndOwner($this->getStudentRole(), $this->getUser());
        return new ViewModel([
            'students' => $students,
        ]);
    }

    public function newAction()
    {
        $request = $this->getRequest();
        $user = new User();
        $form = $this->createForm($user);
        $form->get('group')->setOption(
            'find_method',
            [
                'name' => 'findBy',
                'params' => [
                    'criteria' => [
                        'owner' => $this->getUser(),
                    ],
                ],
            ]
        );
        $form->getInputFilter()->get('email')->getValidatorChain()->attach(new NoObjectExists([
            'object_repository' => $this->getRepository('User'),
            'fields' => [
                'email',
            ],
        ]));
        $validationGroup = [
            'firstName', 'lastName', 'dateOfBirth', 'contactName', 'phone', 'additionalPhone',
            'email', 'group', 'educationPrice', 'educationPriceNote', 'password',
        ];
        $form->setValidationGroup($validationGroup);
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $user
                    ->addRole($this->getStudentRole())
                    ->setPassword($this->encryptPassword($form->get('password')->getValue()))
                    ->setState(1)
                    ->setOwner($this->getUser())
                ;
                $this->getEm()->persist($user);
                $this->getEm()->flush();
                $this->addFlashMessages(['Student has been created']);
                return $this->redirect()->toRoute('admin_students_index', ['action' => 'index']);
            }
        }
        return new ViewModel([
            'form' => $form,
        ]);
    }

    public function editAction()
    {
        /** @var \Core\Entity\User $user */
        $user = $this->getRepository('User')->findOneBy([
            'id' => $this->params()->fromRoute('id'),
            'owner' => $this->getUser(),
        ]);
        if (!$user) {
            return $this->redirect()->toRoute('admin_students_index', ['action' => 'index']);
        }
        $request = $this->getRequest();
        $form = $this->createForm($user);
        $form->get('group')->setOption(
            'find_method',
            [
                'name' => 'findBy',
                'params' => [
                    'criteria' => [
                        'owner' => $this->getUser(),
                    ],
                ],
            ]
        );
        $form->getInputFilter()->get('email')->getValidatorChain()->attach(new UniqueObject([
            'object_repository' => $this->getRepository('User'),
            'id' => $user->getId(),
            'fields' => [
                'email',
            ],
        ]));
        $validationGroup = [
            'firstName', 'lastName', 'dateOfBirth', 'contactName', 'phone', 'additionalPhone',
            'email', 'group', 'educationPrice', 'educationPriceNote',
        ];
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
                $this->addFlashMessages(['Student has been saved']);
                return $this->redirect()->toRoute('admin_students_index', ['action' => 'index']);
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
    protected function getStudentRole()
    {
        return $this->getRepository('Role')->findOneBy(['roleId' => 'student']);
    }

    /**
     * @return \Core\Entity\Role
     */
    protected function getTeacherRole()
    {
        return $this->getRepository('Role')->findOneBy(['roleId' => 'teacher']);
    }
}
