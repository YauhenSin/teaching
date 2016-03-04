<?php

namespace User\Controller;

use Core\Controller\CoreController;
use Core\Entity\CartItem;
use Core\Entity\Wishlist;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class WishlistController extends CoreController
{
    public function indexAction()
    {
        $wishlist = $this->getRepository('Wishlist')->findBy(['user' => $this->getUser()]);
        $paginator = $this->getPaginator(new ArrayCollection($wishlist), $this->params()->fromQuery('page', 1), 6);
        $form = $this->createForm(new CartItem());
        $form->setAttributes([
            'action' => $this->url()->fromRoute('application_cart_index', ['action' => 'add-item']),
        ]);
        return new ViewModel([
            'form' => $form,
            'paginator' => $paginator,
        ]);
    }

    public function addAction()
    {
        $isAjax = $this->getRequest()->isXmlHttpRequest();
        $user = $this->getUser();
        $product = $this->getEntity('Product', $this->params()->fromRoute('id'));
        $wishlist = $this->getRepository('Wishlist')->findOneBy(['user' => $user, 'product' => $product]);
        if ($product && !$wishlist) {
            $wishlist = new Wishlist();
            $wishlist->setUser($user)
                ->setProduct($product)
                ->setStore($this->getServiceLocator()->get('xeira_language_switcher')->getCurrentStore())
                ->setCurrency($this->getServiceLocator()->get('xeira_currency_switcher')->getCurrentCurrency());
            $this->getEm()->persist($wishlist);
            $this->getEm()->flush();
            if ($isAjax) {
                $partial = $this->getServiceLocator()->get('ViewHelperManager')->get('partial');
                $item = $partial('user/wishlist/partials/added-item', [
                    'form' => $this->createForm(new CartItem()),
                    'product' => $product,
                ]);
                return new JsonModel([
                    'success' => true,
                    'item' => $item,
                ]);
            }
            return $this->redirect()->toRoute('user_account_wishlist', ['action' => 'index']);
        }
        if ($isAjax) {
            return new JsonModel([
                'success' => false,
            ]);
        }
        return $this->redirect()->toRoute('user_account_wishlist', ['action' => 'index']);
    }

    public function removeAction()
    {
        $product = $this->getEntity('Product', $this->params()->fromRoute('id'));
        $user = $this->getUser();
        $wishlist = $this->getRepository('Wishlist')->findOneBy(['user' => $user, 'product' => $product]);
        if ($product && $wishlist) {
            $this->getEm()->remove($wishlist);
            $this->getEm()->flush();
            $partialViewHelper = $this->getServiceLocator()->get('ViewHelperManager')->get('partial');
            $item = $partialViewHelper('user/wishlist/partials/removed-item', [
                'product' => $product,
            ]);
            return new JsonModel([
                'success' => true,
                'item' => $item,
                'itemId' => $product->getId(),
            ]);
        }
        return $this->redirect()->toRoute('user_account_wishlist', ['action' => 'index']);
    }

    public function searchAction()
    {
        $wishlists = $this->getRepository('Wishlist')->findBy(['user' => $this->getUser()]);
        $paginator = $this->getPaginator(new ArrayCollection($wishlists), $this->params()->fromQuery('page', 1), 6);
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $paginationControlHelper = $viewHelperManager->get('PaginationControl');
        $partialViewHelper = $viewHelperManager->get('partial');
        return new JsonModel([
            'success' => true,
            'wishlist' => $partialViewHelper('user/wishlist/partials/list', [
                'form' => $this->createForm(new CartItem()),
                'paginator' => $paginator,
            ]),
            'pagination' => $paginationControlHelper($paginator, 'Sliding', 'core/partials/paginator/paginator'),
        ]);
    }
}
