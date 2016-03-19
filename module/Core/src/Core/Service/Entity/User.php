<?php

namespace Core\Service\Entity;

class User extends AbstractEntityService
{
    /**
     * @return \Core\Entity\User
     */
    public function getUser()
    {
        return parent::getEntity();
    }

    /**
     * @return \Core\Entity\Role
     */
    public function getRole()
    {
        $roles = $this->getUser()->getRoles();
        return $roles[0];
    }

    /**
     * @return string
     */
    public function getFirstLastName()
    {
        $user = $this->getUser();
        return $user->getFirstName() . ' ' . $user->getLastName();
    }

    /**
     * @param int $state
     * @return bool
     */
    public function addStateToStack($state)
    {
        $user = $this->getUser();
        $statesStack = $user->getStatesStack();
        if (!in_array($state, $statesStack)) {
            array_push($statesStack, $state);
            $user->setStatesStack($statesStack);
            return true;
        }
        return false;
    }

    /**
     * @param int $state
     * @return bool
     */
    public function removeStateFromStack($state)
    {
        $user = $this->getUser();
        $statesStack = $user->getStatesStack();
        if (in_array($state, $statesStack)) {
            $itemIndex = array_search($state, $statesStack);
            unset($statesStack[$itemIndex]);
            $user->setStatesStack($statesStack);
            return true;
        }
        return false;
    }

}
