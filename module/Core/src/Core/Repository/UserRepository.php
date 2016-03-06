<?php
namespace Core\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class UserRepository extends EntityRepository
{
    /**
     * @param \Core\Entity\Role $role
     * @param \Core\Entity\User $owner
     * @return array
     */
    public function findByRoleAndOwner($role, $owner)
    {
        $queryBuilder = new QueryBuilder($this->getEntityManager());
        $queryBuilder
            ->select('user')
            ->from('\Core\Entity\User', 'user')
            ->where('user.owner = :owner')
            ->setParameter('owner', $owner)
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