<?php

namespace User\Controller;

use Core\Controller\CoreController;
use Core\Entity\UserAddress;
use Core\Traits\CountryInputTrait;
use Zend\Crypt\Password\Bcrypt;
use Zend\Validator\Callback;
use Zend\View\Model\ViewModel;

class SettingsController extends CoreController
{
    use CountryInputTrait;

    public function editProfileAction()
    {
        $userForm = $this->createEditUserForm($this->getUser());
        $address = new UserAddress();
        $addressForm = $this->createAddAddressForm($address);
        $addressForm->get('country')->setAttribute('options', $this->getCountryInputOptions());
        $request = $this->getRequest();
        if ($request->isPost()) {
            $userForm->setData($request->getPost());
            if ($userForm->isValid()) {
                $this->getEm()->flush();
                $this->addFlashMessages(['Profile has been saved.']);
                return $this->redirect()->toRoute('user_account_settings', ['action' => 'edit-profile']);
            }
        }
        return new ViewModel([
            'userForm' => $userForm,
            'addressForm' => $addressForm,
        ]);
    }

    public function addAddressAction()
    {
        $address = new UserAddress();
        $addressForm = $this->createAddAddressForm($address);
        $addressForm->get('country')->setAttribute('options', $this->getCountryInputOptions());
        $request = $this->getRequest();
        if ($request->isPost()) {
            $addressForm->setData($request->getPost());
            if ($addressForm->isValid()) {
                $user = $this->getUser();
                if (!$user->getAddresses()->count()) {
                    $address->setIsDefault(true);
                }
                $address->setUser($this->getUser());
                $this->getEm()->persist($address);
                $this->getEm()->flush();
                $this->addFlashMessages(['Address has been saved.']);
                return $this->redirect()->toRoute('user_account_settings', ['action' => 'edit-profile']);
            }
        }
        $view = new ViewModel();
        $view->setTemplate('user/settings/edit-profile');
        $view->setVariables([
            'userForm' => $this->createEditUserForm($this->getUser()),
            'addressForm' => $addressForm,
        ]);
        return $view;
    }

    public function setDefaultAddressAction()
    {
        $address = $this->getRepository('UserAddress')->findByIdAndUser($this->params()->fromRoute('id'), $this->getUser());
        if ($address) {
            foreach ($this->getUser()->getAddresses() as $userAddress) {
                $userAddress->setIsDefault($userAddress->getId() == $address->getId());
                $this->getEm()->persist($userAddress);
            }
            $this->getEm()->flush();
            $this->addFlashMessages(['Default address has been changed.']);
        }
        return $this->redirect()->toRoute('user_account_settings', ['action' => 'edit-profile']);
    }

    public function editAddressAction()
    {
        $address = $this->getRepository('UserAddress')->findByIdAndUser($this->params()->fromRoute('id'), $this->getUser());
        if (!$address) {
            $this->redirect()->toRoute('user_account_settings', ['action' => 'edit-profile']);
        }
        $addressForm = $this->createForm($address);
        $addressForm->get('country')->setAttribute('options', $this->getCountryInputOptions());
        $request = $this->getRequest();
        if ($request->isPost()) {
            $addressForm->setData($request->getPost());
            if ($addressForm->isValid()) {
                $this->getEm()->flush();
                $this->addFlashMessages(['Address has been saved.']);
                return $this->redirect()->toRoute('user_account_settings', ['action' => 'edit-profile']);
            }
        }
        return new ViewModel([
            'addressForm' => $addressForm,
        ]);
    }

    public function deleteAddressAction()
    {
        $address = $this->getRepository('UserAddress')->findByIdAndUser($this->params()->fromRoute('id'), $this->getUser());
        if ($address) {
            $this->getEm()->remove($address);
            $this->getEm()->flush();
            $this->addFlashMessages(['Address has been deleted.']);
        }
        return $this->redirect()->toRoute('user_account_settings', ['action' => 'edit-profile']);
    }

    public function resetPasswordAction()
    {
        $user = $this->getUser();
        $form = $this->createForm($user);
        $oldPassword = clone $form->get('password');
        $form->add($oldPassword->setName('oldPassword'));
        $oldPasswordValidationCallback = new Callback(function ($value) use ($user) {
            $bcrypt = new Bcrypt;
            $bcrypt->setCost($this->getSl()->get('zfcuser_module_options')->getPasswordCost());
            return $bcrypt->verify($value, $user->getPassword());
        });
        $oldPasswordValidationCallback->setMessage('Incorrect password.');
        $form->getInputFilter()->get('oldPassword')
            ->setRequired(true)
            ->getValidatorChain()->attach($oldPasswordValidationCallback);
        $form->setValidationGroup('oldPassword', 'password', 'passwordConfirm');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $bcrypt = new Bcrypt();
                $bcrypt->setCost($this->getSl()->get('zfcuser_module_options')->getPasswordCost());
                $user->setPassword($bcrypt->create($form->get('password')->getValue()));
                $this->getEm()->persist($user);
                $this->getEm()->flush();
                return $this->redirect()->toRoute('user_account_settings', ['action' => 'edit-profile']);
            }
        }
        return new ViewModel([
            'form' => $form,
        ]);
    }

    /**
     * @param \Core\Entity\UserAddress $address
     * @return \Zend\Form\Form
     */
    protected function createAddAddressForm($address)
    {
        $addressForm = $this->createForm($address);
        $addressForm->setAttributes([
            'action' => $this->url()->fromRoute('user_account_settings', ['action' => 'add-address']),
        ]);
        return $addressForm;
    }

    /**
     * @param \Core\Entity\User $user
     * @return \Zend\Form\Form
     */
    protected function createEditUserForm($user)
    {
        $userForm = $this->createForm($user);
        $userForm->setValidationGroup('firstName', 'lastName', 'phone', 'paypalEmail');
        $userForm->setAttributes([
            'action' => $this->url()->fromRoute('user_account_settings', ['action' => 'edit-profile']),
        ]);
        return $userForm;
    }
}
