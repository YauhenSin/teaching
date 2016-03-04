<?php

namespace User\Controller;

use Core\Controller\CoreController;
use Core\Entity\Design;
use Core\Entity\DesignTag;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class DesignsController extends CoreController
{
    public function indexAction()
    {
        $designs = $this->getRepository('Design')->searchByUserCategoryStatusesName($this->getUser(), null, [Design::STATUS_ACTIVE, Design::STATUS_REJECTED]);
        $paginator = $this->getPaginator(new ArrayCollection($designs), $this->params()->fromQuery('page', 1));
        $view = new ViewModel();
        if (!$this->getUser()->getPaypalEmail()) {
            $view->setTemplate('user/designs/paypal');
        } elseif ($paginator->getTotalItemCount() > 0 ) {
            $view->setTemplate('user/designs/index');
            $form = $this->createForm(new Design());
            $form->get('category')->setOption(
                'find_method',
                [
                    'name' => 'findCategoriesWithDesignsByUser',
                    'params' => [
                        'user' => $this->getUser(),
                    ],
                ]
            );
            $view->setVariables([
                'paginator' => $paginator,
                'form' => $form,
            ]);
        } else {
            $view->setTemplate('user/designs/empty');
        }
        return $view;
    }

    public function newAction()
    {
        $design = new Design();
        return new ViewModel([
            'commissionAmount' => $this->getCommissionAmount(),
            'design' => $design,
            'form' => $this->createForm($design),
        ]);
    }

    public function editAction()
    {
        $design = $this->getRepository('Design')->findByIdAndUser($this->params()->fromRoute('id'), $this->getUser());
        if (!$design || $design->getStatus() == Design::STATUS_REJECTED) {
            $this->redirect()->toRoute('user_account_designs', ['action' => 'index']);
        }
        $form = $this->createForm($design);
        $view = new ViewModel();
        $view->setTemplate('user/designs/new');
        $view->setVariables([
            'commissionAmount' => $this->getCommissionAmount(),
            'design' => $design,
            'form' => $form,
        ]);
        return $view;
    }

    public function fileUploadAction()
    {
        $user = $this->getUser();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->removeNewDesigns();
            $uploadedFile = $request->getFiles()->get('file');

            //upload file to the server and create File entity
            $uploadResult = $this->getUploadFilePlugin()->upload($this->getUser(), $uploadedFile, 'designs');
            if (!$uploadResult['success']) {
                return new JsonModel([
                    'success' => false,
                    'message' => $uploadResult['message'],
                ]);
            }

            //create Image entity from uploaded file
            $image = $this->getUploadFilePlugin()->generateImageEntity($uploadResult['file']);
            $this->getUploadFilePlugin()->generateImageFile($image, 'designs', 'preview');

            if ($this->params()->fromRoute('id')) {
                $design = $this->getRepository('Design')->findByIdAndUser($this->params()->fromRoute('id'), $user);
                $design->setImage($image);
            } else {
                $design = new Design();
                $design
                    ->setName('default_name')
                    ->setStatus(Design::STATUS_NEW)
                    ->setImage($image)
                    ->setPrice(0)
                    ->setUser($user);
            }
            $this->getEm()->persist($design);
            $this->getEm()->flush();
            return new JsonModel([
                'success' => true,
                'image' => $image->getFile()->getRelativeUrl(),
            ]);
        }
        return new JsonModel([
            'success' => false,
        ]);
    }

    public function termsConfirmAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $terms = $request->getPost()->get('terms');
            $errors = [];
            if (!(bool) $terms) {
                $errors['terms'][] = $this->translate('Not confirmed');
            }
            if (count($errors)) {
                return new JsonModel([
                    'success' => false,
                    'errors' => $errors,
                ]);
            }
            return new JsonModel([
                'success' => true,
            ]);
        }
        return new JsonModel([
            'success' => false,
        ]);
    }

    public function descriptionAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            /** @var $design Design */
            if ($this->params()->fromRoute('id')) {
                $design = $this->getRepository('Design')->findByIdAndUser($this->params()->fromRoute('id'), $this->getUser());
            } else {
                $design = $this->getRepository('Design')->findOneNewByUser($this->getUser());
            }
            $form = $this->createForm($design);
            $form->setData($request->getPost())->setValidationGroup(['name', 'category']);
            $errors = [];
            $tags = explode(',', $request->getPost()->get('tags'));
            if (count($tags) < 3) {
                $errors['tags'][] = $this->translate('Specify at least 3 tags for your design');
            }
            if ($form->isValid() && !count($errors)) {
                foreach ($design->getTags() as $tag) {
                    $design->removeTag($tag);
                }
                foreach ($tags as $tagName) {
                    $tagName = trim($tagName);
                    $tag = $this->getRepository('DesignTag')->findOneBy([
                        'name' => $tagName,
                    ]);
                    if (!$tag) {
                        $tag = new DesignTag();
                        $tag->setName($tagName);
                        $this->getEm()->persist($tag);
                        $this->getEm()->flush();
                    }
                    $design->addTag($tag);
                    $this->getEm()->persist($design);
                    $this->getEm()->flush();
                }
                return new JsonModel([
                    'success' => true,
                    'design' => [
                        'id' => $design->getId(),
                        'name' => $design->getName(),
                        'type' => $design->getImage()->getFile()->getType(),
                        'size_px' => $design->getImage()->getWidth() . ' px x ' . $design->getImage()->getHeight() . ' px',
                    ],
                ]);
            }
            $errors['name'] = $form->get('name')->getMessages();
            $errors['category'] = $form->get('category')->getMessages();
            return new JsonModel([
                'success' => false,
                'errors' => $errors,
            ]);
        }
        return new JsonModel([
            'success' => false,
        ]);
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            /** @var $design Design */
            if ($this->params()->fromRoute('id')) {
                $design = $this->getRepository('Design')->findByIdAndUser($this->params()->fromRoute('id'), $this->getUser());
            } else {
                $design = $this->getRepository('Design')->findOneNewByUser($this->getUser());
            }
            $form = $this->createForm($design);
            $form->setData($request->getPost())->setValidationGroup(['price']);
            if ($form->isValid()) {
                $design->setStatus(Design::STATUS_ACTIVE);
                $this->getEm()->persist($design);
                $this->getEm()->flush();
                return new JsonModel([
                    'success' => true,
                    'redirect' => $this->url()->fromRoute('user_account_designs', ['action' => 'index']),
                ]);
            }
            $errors = [];
            $errors['price'] = $form->get('price')->getMessages();
            return new JsonModel([
                'success' => false,
                'errors' => $errors,
            ]);
        }
        return new JsonModel([
            'success' => false,
        ]);
    }

    public function deleteAction()
    {
        $design = $this->getRepository('Design')->findByIdAndUser($this->params()->fromRoute('id'), $this->getUser());
        if ($design) {
            $this->getEm()->remove($design);
            $this->getEm()->flush();
        }
        $this->redirect()->toRoute('user_account_designs', ['action' => 'index']);
        return new ViewModel();
    }

    public function searchAction()
    {
        $user = $this->getUser();
        $statuses = [Design::STATUS_ACTIVE, Design::STATUS_REJECTED];
        $category = $this->getEntity('DesignCategory', $this->params()->fromQuery('category'));
        $name = $this->params()->fromQuery('name', null);
        $designs = $this->getRepository('Design')->searchByUserCategoryStatusesName($user, $category, $statuses, $name);
        $paginator = $this->getPaginator(new ArrayCollection($designs), $this->params()->fromQuery('page', 1));
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $paginationControlHelper = $viewHelperManager->get('PaginationControl');
        $partialViewHelper = $viewHelperManager->get('partial');
        return new JsonModel([
            'success' => true,
            'designs' => $partialViewHelper('user/designs/partials/user-designs-list', $paginator->getCurrentItems()->getArrayCopy()),
            'pagination' => $paginationControlHelper($paginator, 'Sliding', 'core/partials/paginator/paginator'),
        ]);
    }

    protected function removeNewDesigns()
    {
        /** @var $newDesigns Design [] */
        $newDesigns = $this->getRepository('Design')->findAllNewByUser($this->getUser());
        foreach ($newDesigns as $design) {
            $fileDir = $design->getImage()->getFile()->getMainPath();
            if ($handle = opendir($fileDir)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        @unlink($fileDir . $entry);
                    }
                }
                closedir($handle);
            }
            @rmdir($fileDir);
            $designImage = $design->getImage();
            $designFile = $designImage->getFile();
            $this->getEm()->remove($design);
            $this->getEm()->remove($designImage);
            $this->getEm()->remove($designFile);
            $this->getEm()->flush();
        }
    }

    protected function getCommissionAmount()
    {
        $commissionAmount = 0;
        $commissionSettingValue = $this->getSl()->get('xeira_admin_settings')->getSettingValue('design_commission');
        if ($commissionSettingValue !== null) {
            $commissionAmount = $commissionSettingValue;
        }
        return $commissionAmount;
    }
}
