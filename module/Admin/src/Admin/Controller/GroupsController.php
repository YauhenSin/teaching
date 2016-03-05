<?php

namespace Admin\Controller;

use Core\Controller\CoreController;
use Core\Entity\Group;
use Core\Entity\User;
use Zend\View\Model\ViewModel;

class GroupsController extends CoreController
{
    public function indexAction()
    {
        $groups = $this->getRepository('Group')->findAll();
        return new ViewModel([
            'groups' => $groups,
        ]);
    }

    public function newAction()
    {
        $request = $this->getRequest();
        $group = new Group();
        $form = $this->createForm($group);
        $form->get('teacher')->getProxy()->setLabelGenerator(
            function (User $teacher) {
                return $teacher->getService()->getFirstLastName();
            });
        $form->get('teacher')->setOption(
            'find_method',
            [
                'name' => 'findByRole',
                'params' => [
                    'role' => $this->getTeacherRole(),
                ],
            ]
        );
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getEm()->persist($group);
                $this->getEm()->flush();
                $this->addFlashMessages(['Group has been created']);
                return $this->redirect()->toRoute('admin_groups_index', ['action' => 'index']);
            }
        }
        return new ViewModel([
            'form' => $form,
        ]);
    }

    public function editAction()
    {
        /** @var \Core\Entity\Group $group */
        $group = $this->getRepository('Group')->findOneBy(['id' => $this->params()->fromRoute('id')]);
        if (!$group) {
            return $this->redirect()->toRoute('admin_groups_index', ['action' => 'index']);
        }
        $request = $this->getRequest();
        $form = $this->createForm($group);
        $form->get('teacher')->getProxy()->setLabelGenerator(
            function (User $teacher) {
                return $teacher->getService()->getFirstLastName();
            });
        $form->get('teacher')->setOption(
            'find_method',
            [
                'name' => 'findByRole',
                'params' => [
                    'role' => $this->getTeacherRole(),
                ],
            ]
        );
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getEm()->persist($group);
                $this->getEm()->flush();
                $this->addFlashMessages(['Group has been saved']);
                return $this->redirect()->toRoute('admin_groups_index', ['action' => 'index']);
            }
        }
        return new ViewModel([
            'form' => $form,
        ]);
    }

    /**
     * @return \Core\Entity\Role
     */
    protected function getTeacherRole()
    {
        return $this->getRepository('Role')->findOneBy(['roleId' => 'teacher']);
    }
}
