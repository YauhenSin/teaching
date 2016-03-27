<?php

namespace Admin\Controller;

use Core\Controller\CoreController;
use Core\Entity\Group;
use Core\Entity\Homework;
use Core\Entity\User;
use Zend\View\Model\ViewModel;

class GroupsController extends CoreController
{
    public function indexAction()
    {
        $groups = $this->getRepository('Group')->findBy(['owner' => $this->getUser()]);
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
                'name' => 'findByRoleAndOwner',
                'params' => [
                    'role' => $this->getTeacherRole(),
                    'owner' => $this->getUser(),
                ],
            ]
        );
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $group->setOwner($this->getUser());
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
        $group = $this->getRepository('Group')->findOneBy([
            'id' => $this->params()->fromRoute('id'),
            'owner' => $this->getUser(),
        ]);
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
                'name' => 'findByRoleAndOwner',
                'params' => [
                    'role' => $this->getTeacherRole(),
                    'owner' => $this->getUser(),
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

    public function viewAction()
    {
        /** @var \Core\Entity\Group $group */
        $group = $this->getRepository('Group')->findOneBy([
            'id' => $this->params()->fromRoute('id'),
            'owner' => $this->getUser(),
        ]);
        if (!$group) {
            return $this->redirect()->toRoute('admin_groups_index', ['action' => 'index']);
        }
        $homework = $this->getRepository('Homework')->findOneBy([
            'group' => $group,
            'state' => Homework::STATE_ACTIVE,
        ]);
        return new ViewModel([
            'group' => $group,
            'homework' => $homework,
        ]);
    }

    public function deleteAction()
    {
        /** @var \Core\Entity\Group $group */
        $group = $this->getEntity('Group', $this->params()->fromRoute('id'));
        if ($group && $group->getOwner() == $this->getUser()) {
            try {
                $this->getEm()->remove($group);
                $this->getEm()->flush();
                $this->addFlashMessages(['Group has been deleted']);
            } catch(\Exception $exception) {
                $this->addFlashMessages(['Need to remove all related teachers, students etc.'], 'error');
            }
            return $this->redirect()->toRoute('admin_groups_index', ['action' => 'index']);
        }
        return new ViewModel([
        ]);
    }

    public function addHomeworkAction()
    {
        /** @var \Core\Entity\Group $group */
        $group = $this->getRepository('Group')->findOneBy([
            'id' => $this->params()->fromRoute('id'),
            'owner' => $this->getUser(),
        ]);
        if (!$group) {
            return $this->redirect()->toRoute('admin_groups_index', ['action' => 'index']);
        }
        $homework = new Homework();
        $request = $this->getRequest();
        $form = $this->createForm($homework);
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                foreach ($group->getHomeworks() as $pastHomework) {
                    $pastHomework->setState(Homework::STATE_PAST);
                    $this->getEm()->persist($pastHomework);
                }
                $homework
                    ->setGroup($group)
                    ->setState(Homework::STATE_ACTIVE)
                ;
                $this->getEm()->persist($homework);
                $this->getEm()->flush();
                $this->addFlashMessages(['Homework has been added']);
                $this->getEventManager()->trigger(\Core\Service\Mailing::HOMEWORK, $this, ['homework' => $homework]);
                return $this->redirect()->toRoute('admin_groups_index', ['action' => 'view', 'id' => $group->getId()]);
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
