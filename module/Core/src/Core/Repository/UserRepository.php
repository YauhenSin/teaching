<?php
namespace Core\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class UserRepository extends EntityRepository
{
    /**
     * @param \Core\Entity\Role $role
     * @return array
     */
    public function findByRole($role)
    {
        $queryBuilder = new QueryBuilder($this->getEntityManager());
        $queryBuilder
            ->select('user')
            ->from('\Core\Entity\User', 'user')
        ;
        $queryBuilder->andWhere(
            $queryBuilder
                ->setParameter('role', $role)
                ->expr()->isMemberOf(':role', 'user.roles')
        );
        $result = $queryBuilder->getQuery()->getResult();
        return $result;
    }
}