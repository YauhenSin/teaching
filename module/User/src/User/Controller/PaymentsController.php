<?php

namespace User\Controller;

use Core\Controller\CoreController;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\View\Model\ViewModel;
use Core\Entity\OrderItemDesign;
use Core\Entity\Payment;
use Core\Utils\DatesUtils;

class PaymentsController extends CoreController
{
    /**
     * @var OrderItemDesign []  
     */
    protected $candidates;

    const MIN_BALANCE_ALLOWED = 50;
    const MIN_BALANCE_ALLOWED_ERR = 'The minimum balance allowed for create invoice is %s';
    const PAYPAL_EMAIL_EMPTY_ERR = 'Please enter PayPal account email';
    const MANUAL_MODE_NOT_ALLOWED_ERR = 'You can one create invoice at quarter';
    
    public function indexAction()
    {
        $balance = $this->getBalance();

        $payments = $this->getRepository('Payment')->findByUser($this->getUser());
        $paginator = $this->getPaginator(new ArrayCollection($payments), $this->params()->fromQuery('page', 1));

        return new ViewModel([
            'paginator' => $paginator,
            'balance' => $balance,
            'is_manual_mode' => $this->isAllowManualMode(),
        ]);
    }

    public function createInvoiceAction(){
        if($this->getBalance() < self::MIN_BALANCE_ALLOWED){
            $currencySwitcherViewHelper = $this->getServiceLocator()->get('ViewHelperManager')->get('currencySwitcher');
            $minBalanceAllowed = $currencySwitcherViewHelper->currencyFormat(self::MIN_BALANCE_ALLOWED);
            $this->addFlashMessages([sprintf(self::MIN_BALANCE_ALLOWED_ERR, $minBalanceAllowed)], 'error');
            return $this->redirect()->toRoute('user_account_payments', ['action' => 'index']);
        }

        if (!$this->getUser()->getPaypalEmail()){
            $this->addFlashMessages([self::PAYPAL_EMAIL_EMPTY_ERR], 'error');
            return $this->redirect()->toRoute('user_account_settings', ['action' => 'edit-profile']);
        }

        if(!$this->isAllowManualMode()){
            $this->addFlashMessages([self::MANUAL_MODE_NOT_ALLOWED_ERR], 'error');
            return $this->redirect()->toRoute('user_account_payments', ['action' => 'index']);
        }

        $payment = new Payment();
        $payment
            ->setUser($this->getUser())
            ->setPaypalEmail($this->getUser()->getPaypalEmail())
            ->setStatus(Payment::PAYMENT_STATUS_PENDING)
            ->setAmount($this->getBalance())
            ->setMode(Payment::MODE_MANUAL)
        ;

        foreach($this->getCandidates() as $item){
            $item->setPayment($payment);
            $this->getEm()->persist($item);
        }

        $this->getEm()->persist($payment);
        $this->getEm()->flush();

        return $this->redirect()->toRoute('user_account_payments', ['action' => 'index']);
    }

    /**
     * Calculate current user balance
     *
     * @return float
     */
    protected function getBalance(){
        $balance = 0;
        foreach ($this->getCandidates() as $item) {
            $balance += $item->getBasePrice();
        }
        return $balance;
    }

    /**
     *
     * @return Core\Entity\OrderItemDesign []
     */
    protected function getCandidates(){
        if(!$this->candidates){
            $this->candidates = $this->getRepository('OrderItemDesign')->findByDesignOwnerAndNotPayment($this->getUser(), OrderItemDesign::STATUS_PAID);
        }
        return $this->candidates;
    }

    /**
     * Check allowed manual mode for create invoice
     * 
     * @return bool
     */
    protected function isAllowManualMode(){
        $datesOfQuarter = DatesUtils::getDatesOfQuarter('current', null, 'Y-m-d H:i:s');
        $payment = $this->getRepository('Payment')->findByModeDateStartDateEnd(Payment::MODE_MANUAL, null, $datesOfQuarter['start'], $datesOfQuarter['end']);
        if(!$payment){
            return true;
        }
        return false;
    }
}