<?php

namespace Core\Service\Entity;

class AbstractEntityService
{
    private $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    public function getEntity()
    {
        return $this->entity;
    }
}

