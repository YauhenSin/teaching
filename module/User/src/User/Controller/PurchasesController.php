<?php

namespace User\Controller;

use Core\Controller\CoreController;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class PurchasesController extends CoreController
{
    public function indexAction()
    {
        $orders = $this->getRepository('Order')->findBy([
            'user' => $this->getUser(),
        ]);
        $paginator = $this->getPaginator(new ArrayCollection($orders), $this->params()->fromQuery('page', 1));
        return new ViewModel([
            'paginator' => $paginator,
        ]);
    }

    public function viewAction()
    {
        $order = $this->getRepository('Order')->findOneBy([
            'id' => $this->params()->fromRoute('id'),
            'user' => $this->getUser(),
        ]);
        if (!$order) {
            $this->redirect()->toRoute('user_account_purchases', ['action' => 'index']);
        }
        return new ViewModel([
            'order' => $order,
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
        $orderId = $this->params()->fromQuery('orderId', null);
        $orders = $this->getRepository('Order')->findByUserIdDateStartDateEnd($this->getUser(), $orderId, $startDate, $endDate);
        $paginator = $this->getPaginator(new ArrayCollection($orders), $this->params()->fromQuery('page', 1));
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $paginationControlHelper = $viewHelperManager->get('PaginationControl');
        $partialViewHelper = $viewHelperManager->get('partial');
        return new JsonModel([
            'success' => true,
            'items' => $partialViewHelper('user/purchases/partials/list', [
                'paginator' => $paginator,
            ]),
            'pagination' => $paginationControlHelper($paginator, 'Sliding', 'core/partials/paginator/paginator'),
        ]);
    }
}
