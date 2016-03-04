<?php

namespace User\Controller;

use Core\Controller\CoreController;
use Core\Entity\Review;
use Zend\View\Model\ViewModel;

class ReviewsController extends CoreController
{
    public function indexAction()
    {
        return new ViewModel([
            'reviews' => $this->generateReviewForms(),
        ]);
    }

    public function editAction()
    {
        $review = $this->getRepository('Review')->findNewByIdAndUser($this->params()->fromRoute('id'), $this->getUser());
        if (!$review) {
            $this->redirect()->toRoute('user_account_reviews', ['action' => 'index']);
        }
        $request = $this->getRequest();
        $reviewForm = $this->createForm($review)->setValidationGroup('content');
        if ($request->isPost()) {
            $reviewForm->setData($request->getPost());
            if ($reviewForm->isValid()) {
                $review->setStatus(Review::STATUS_PENDING);
                $this->getEm()->persist($review);
                $this->getEm()->flush();
                $this->redirect()->toRoute('user_account_reviews', ['action' => 'index']);
            }
        }
        $reviews = $this->generateReviewForms();
        $reviews[$review->getId()] = [
            'form' => $reviewForm->prepare(),
            'product' => $review->getProduct(),
        ];
        $view = new ViewModel();
        $view->setTemplate('user/reviews/index');
        $view->setVariables([
            'reviews' => $reviews,
        ]);
        return $view;
    }

    public function generateAction()
    {
        $review = new Review();
        $review
            ->setUser($this->getUser())
            ->setContent('')
            ->setRating(0)
            ->setStatus(Review::STATUS_NEW)
            ->setAuthorType(Review::AUTHOR_TYPE_USER)
        ;
        $this->getEm()->persist($review);
        $this->getEm()->flush();
        $this->redirect()->toRoute('user_account_reviews', ['action' => 'index']);
        return new ViewModel();
    }

    protected function generateReviewForms()
    {
        $userReviews = $this->getRepository('Review')->findNewByUser($this->getUser());
        $reviews = [];
        foreach ($userReviews as $userReview) {
            $reviewForm = $this->createForm($userReview);
            $reviewForm->setAttributes([
                'action' => $this->url()->fromRoute('user_account_reviews', ['action' => 'edit', 'id' => $userReview->getId()]),
            ]);
            $reviews[$userReview->getId()] = [
                'form' => $reviewForm->prepare(),
                'product' => $userReview->getProduct(),
            ];
        }
        return $reviews;
    }
}
