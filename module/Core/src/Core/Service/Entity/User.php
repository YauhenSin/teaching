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
}
