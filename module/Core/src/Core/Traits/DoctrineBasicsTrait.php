<?php
namespace Core\Traits;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as Hydrator;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

trait DoctrineBasicsTrait
{
    use ServiceLocatorAwareTrait;

    /**
     * Get Doctrine Entity Manger
     *
     * @return EntityManager
     */
    protected function getEm()
    {
        return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    }

    /**
     * Get Doctrine Entity by id
     *
     * @param string $repository
     * @param int $id 
     * 
     * @return object
     */
    protected function getEntity($repository, $id)
    {
        return $this->getEm()->find('\\Core\\Entity\\' . $repository, intval($id));
    }

    /**
     * Get Doctrine repository
     *
     * @param string $repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepository($repository)
    {
        return $this->getEm()->getRepository('\\Core\\Entity\\' . $repository);
    }

    /**
     * Shortcut to create a form from annotations
     * Attach Doctrine hydrator
     *
     * @param object $entity
     * @param bool $bind - if entity should be binded
     *
     * @return \Zend\Form\Form;
     */
    protected function createForm($entity, $bind = true) {
        $builder = new AnnotationBuilder();
        $form = $builder->createForm($entity);
        // for DoctrineORMModule\Form\Element\EntitySelect annotation
        foreach ($form->getElements() as $element) {
            if (method_exists($element, 'getProxy')) {
                $proxy = $element->getProxy();
                if (method_exists($proxy, 'setObjectManager')) {
                    $proxy->setObjectManager($this->getEm());
                }
            }
        }
        //
        $form->setHydrator(new Hydrator($this->getEm(), get_class($entity)));
        if ($bind === true) {
            $form->bind($entity);
        }
        return $form;
    }
}