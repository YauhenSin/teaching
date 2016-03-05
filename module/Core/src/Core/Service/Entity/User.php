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
}
