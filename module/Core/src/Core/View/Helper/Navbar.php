<?php

namespace Core\View\Helper;

use Core\Traits\DoctrineBasicsTrait;
use Zend\View\Helper\AbstractHelper;

class Navbar extends AbstractHelper
{
    use DoctrineBasicsTrait;

    public function __invoke()
    {
        $this->setServiceLocator($this->getView()->getHelperPluginManager()->getServiceLocator());
        return $this;
    }

    public function render()
    {
        /** @var \Zend\Authentication\AuthenticationService $authService */
        $authService = $this->getServiceLocator()->get('zfcuserauthservice');

        /** @var \Core\Entity\User $user */
        $user = $authService->getIdentity();
        $roleIdentifier = 'guest';
        if ($user) {
            $role = $user->getService()->getRole();
            $roleIdentifier = $role->getRoleId();
        }

        /** @var \Zend\View\Helper\Partial $partialViewHelper */
        $partialViewHelper = $this->getView()->getHelperPluginManager()->get('partial');
        $template = 'partials/navigation/' . $roleIdentifier;
        $variables = [];
        return  $partialViewHelper($template, $variables);
    }
}