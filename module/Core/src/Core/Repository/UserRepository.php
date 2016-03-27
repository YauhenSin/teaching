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

    /**
     * @param \Core\Entity\User $teacher
     * @return array
     */
    public function findStudentsByTeacher($teacher)
    {
        $queryBuilder = new QueryBuilder($this->getEntityManager());
        $queryBuilder
            ->select('u')
            ->from('\Core\Entity\User', 'u')
            ->innerJoin('\Core\Entity\Group', 'g', 'WITH', $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('u.group', 'g'),
                $queryBuilder->setParameter('teacher', $teacher)->expr()->eq('g.teacher', ':teacher')
            ))
        ;
        $result = $queryBuilder->getQuery()->getResult();
        return $result;
    }

    /**
     * @param \Core\Entity\User $teacher
     * @param int $studentId
     * @return \Core\Entity\User | null
     */
    public function findStudentByTeacherAndId($teacher, $studentId)
    {
        $queryBuilder = new QueryBuilder($this->getEntityManager());
        $queryBuilder
            ->select('u')
            ->from('\Core\Entity\User', 'u')
            ->innerJoin('\Core\Entity\Group', 'g', 'WITH', $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('u.group', 'g'),
                $queryBuilder->setParameter('teacher', $teacher)->expr()->eq('g.teacher', ':teacher')
            ))
            ->andWhere('u.id = :studentId')
            ->setParameter('studentId', $studentId)
        ;
        try {
            $result = $queryBuilder->getQuery()->getSingleResult();
        } catch (\Exception $e) {
            return null;
        }
        return $result;
    }
}