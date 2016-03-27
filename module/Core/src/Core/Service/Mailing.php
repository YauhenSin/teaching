<?php

namespace Core\Service;

use Core\Traits\DoctrineBasicsTrait;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;


class Mailing implements ListenerAggregateInterface
{
    const HOMEWORK = 'homework';

    use DoctrineBasicsTrait;

    private $listeners = [];

    public function __invoke($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $sharedEvents->attach('Admin\Controller\GroupsController', self::HOMEWORK, [$this, 'homework'], 100);
        $this->listeners[] = $sharedEvents->attach('Teacher\Controller\GroupsController', self::HOMEWORK, [$this, 'homework'], 100);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * @param $e \Zend\EventManager\EventInterface
     * @return bool
     */
    public function homework(EventInterface $e)
    {
        $params = $e->getParams();
        if (!isset($params['homework'])) {
            return false;
        }
        /** @var \Core\Entity\Homework $homework */
        $homework = $params['homework'];

        $templateVariables = [
            'homework' => $homework,
        ];
        $template = 'partials/mailing/homework';

        $subject = 'New homework email';

        $emails = [];
        foreach ($homework->getGroup()->getStudents() as $student) {
            $emails[] = $student->getEmail();
        }
        $this->send($emails, $subject, $template, $templateVariables);
        return true;
    }

    protected function send($to, $subject, $template, $templateVariables)
    {
        $mailService = $this->getServiceLocator()->get('goaliomailservice_message');
        $email = $mailService->createHtmlMessage($this->getFrom(), $to, $subject, $template, $templateVariables);
        $mailService->send($email);
    }

    protected function getFrom()
    {
        return [
            'email' => 'no-reply@teaching.artprastora.by',
            'name' => 'teaching.artprastora.by',
        ];
    }

    /**
     * @return \Zend\View\Helper\Partial
     */
    protected function getPartialViewHelper()
    {
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        return $viewHelperManager->get('partial');
    }

    /**
     * @param string $route
     * @param array $routeParams
     * @param array $options
     * @return string
     */
    protected function url($route, $routeParams, $options = [])
    {
        /** @var \Zend\View\Helper\Url $urlViewHelper */
        $urlViewHelper = $this->getViewHelperManager()->get('url');
        return $urlViewHelper($route, $routeParams, $options);
    }

    /**
     * @return \Zend\View\HelperPluginManager
     */
    protected function getViewHelperManager()
    {
        return $this->getServiceLocator()->get('ViewHelperManager');
    }
}
