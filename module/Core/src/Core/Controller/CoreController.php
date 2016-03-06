<?php
namespace Core\Controller;

use Core\Traits\DoctrineBasicsTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;
use Zend\Paginator\Paginator as ZendPaginator;
use DoctrineModule\Paginator\Adapter\Collection as DoctrinePaginatorAdapterCollection;

class CoreController extends AbstractActionController
{
    use DoctrineBasicsTrait;

    /**
     * Execute the request
     *
     * @param  MvcEvent $e
     * @return mixed
     * @throws Exception\DomainException
     */
    public function onDispatch(MvcEvent $e)
    {
        parent::onDispatch($e);
        $this->getEventManager()->trigger('setLayout', $e);
    }

    /**
     * @param $collection
     * @param int $currentPageNumber
     * @param int $perPageCount
     * @return ZendPaginator
     */
    protected function getPaginator($collection, $currentPageNumber = 1, $perPageCount = 15)
    {
        $doctrinePaginatorAdapterCollection = new DoctrinePaginatorAdapterCollection($collection);
        $paginator = new ZendPaginator($doctrinePaginatorAdapterCollection);
        $paginator->setDefaultItemCountPerPage($perPageCount);
        $paginator->setCurrentPageNumber($currentPageNumber);
        return $paginator;
    }

    /**
     * @param array $messages
     * @param string $namespace
     */
    public function addFlashMessages($messages, $namespace = 'success')
    {
        foreach ($messages as $message) {
            $this->flashMessenger()->addMessage($message, $namespace);
        }
    }

    /**
     * @param string $text
     * @return string
     */
    public function translate($text)
    {
        $translateViewHelperManager = $this->getServiceLocator()->get('ViewHelperManager')->get('translate');
        return $translateViewHelperManager($text);
    }

    /**
     * Get current user
     *
     * @return \Core\Entity\User
     */
    protected function getUser()
    {
        return $this->getServiceLocator()->get('zfcuserauthservice')->getIdentity();
    }
}
