<?php

namespace User\Controller;

use Core\Controller\CoreController;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class SalesController extends CoreController
{
    public function indexAction()
    {
        $orders = $this->getRepository('OrderItem')->findByProductOwner($this->getUser());
        $paginator = $this->getPaginator(new ArrayCollection($orders), $this->params()->fromQuery('page', 1));
        return new ViewModel([
            'paginator' => $paginator,
        ]);
    }

    public function viewAction()
    {
        $orderItem = $this->getRepository('OrderItem')->findOneBy([
            'id' => $this->params()->fromRoute('id'),
        ]);
        if (!$orderItem->getProduct()->getUser()->getId() == $this->getUser()->getId()) {
            $this->redirect()->toRoute('user_account_sales', ['action' => 'index']);
        }
        return new ViewModel([
            'orderItem' => $orderItem,
        ]);
    }

    public function searchAction()
    {
        $startDate = null;
        $date = new \DateTime();
        if ($searchDate = $date->createFromFormat('m/d/Y H:i:s', $this->params()->fromQuery('startDate') . '00:00:00')) {
            $startDate = clone $searchDate;
        }
        $endDate = null;
        if ($searchDate = $date->createFromFormat('m/d/Y H:i:s', $this->params()->fromQuery('endDate') . '23:59:59')) {
            $endDate = clone $searchDate;
        }
        $itemId = $this->params()->fromQuery('itemId', null);
        $status = $this->params()->fromQuery('status', null);
        $items = $this->getRepository('OrderItem')->findByUserIdStatusDateStartDateEnd($this->getUser(), $itemId, $status, $startDate, $endDate);
        $paginator = $this->getPaginator(new ArrayCollection($items), $this->params()->fromQuery('page', 1));
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $paginationControlHelper = $viewHelperManager->get('PaginationControl');
        $partialViewHelper = $viewHelperManager->get('partial');
        return new JsonModel([
            'success' => true,
            'items' => $partialViewHelper('user/sales/partials/list', [
                'paginator' => $paginator,
            ]),
            'pagination' => $paginationControlHelper($paginator, 'Sliding', 'core/partials/paginator/paginator'),
        ]);
    }
}
