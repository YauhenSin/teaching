<?php

namespace Core\Validator;
use DoctrineModule\Validator\NoObjectExists;

/**
 * Class that validates if objects does not exist in a given repository with a given list of matched fields
 */
class UniqueObject extends NoObjectExists
{
    /**
     * {@inheritDoc}
     */
    public function isValid($value)
    {
        $cleanedValue = $this->cleanSearchValue($value);
        $match        = $this->objectRepository->findOneBy($cleanedValue);

        if ($match && $match->getId() != $this->getOption('id')) {
            $this->error(self::ERROR_OBJECT_FOUND, $value);

            return false;
        }

        return true;
    }
}
