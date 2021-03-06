<?php

namespace Core\Controller;

use Zend\Mvc\Application;
use Zend\Mvc\Router\RouteInterface;
use Zend\Mvc\Router\Exception;
use Zend\Http\PhpEnvironment\Response;
use ZfcUser\Options\ModuleOptions;

use Core\Entity\Role;

/**
 * Buils a redirect response based on the current routing and parameters
 */
class RedirectCallback
{
    /** @var RouteInterface  */
    private $router;

    /** @var Application */
    private $application;

    /** @var ModuleOptions */
    private $options;

    /**
     * @param Application $application
     * @param RouteInterface $router
     * @param ModuleOptions $options
     */
    public function __construct(Application $application, RouteInterface $router, ModuleOptions $options)
    {
        $this->router = $router;
        $this->application = $application;
        $this->options = $options;
    }

    /**
     * @return Response
     */
    public function __invoke()
    {
        $routeMatch = $this->application->getMvcEvent()->getRouteMatch();
        $redirect = $this->getRedirect($routeMatch->getMatchedRouteName(), $this->getRedirectRouteFromRequest());

        $response = $this->application->getResponse();
        $response->getHeaders()->addHeaderLine('Location', $redirect);
        $response->setStatusCode(302);
        return $response;
    }

    /**
     * Return the redirect from param.
     * First checks GET then POST
     * @return string
     */
    private function getRedirectRouteFromRequest()
    {
        $request  = $this->application->getRequest();
        $redirect = $request->getQuery('redirect');
        if ($redirect && $this->routeExists($redirect)) {
            return $redirect;
        }

        $redirect = $request->getPost('redirect');
        if ($redirect && $this->routeExists($redirect)) {
            return $redirect;
        }

        return false;
    }

    /**
     * @param $route
     * @return bool
     */
    private function routeExists($route)
    {
        try {
            $this->router->assemble([], ['name' => $route]);
        } catch (Exception\RuntimeException $e) {
            return false;
        }
        return true;
    }

    /**
     * Returns the url to redirect to based on current route.
     * If $redirect is set and the option to use redirect is set to true, it will return the $redirect url.
     *
     * @param string $currentRoute
     * @param bool $redirect
     * @return mixed
     */
    protected function getRedirect($currentRoute, $redirect = false)
    {
        $useRedirect = $this->options->getUseRedirectParameterIfPresent();
        $routeExists = ($redirect && $this->routeExists($redirect));
        if (!$useRedirect || !$routeExists) {
            $redirect = false;
        }

        switch ($currentRoute) {
            case 'zfcuser/register':
            case 'zfcuser/login':
            case 'zfcuser/authenticate':
                /** @var \Zend\Authentication\AuthenticationService $authService */
                $authService = $this->getApplication()->getServiceManager()->get('zfcuserauthservice');
                /** @var \Core\Entity\User $user */    
                $user = $authService->getIdentity();
                $loginRedirectRoute = $this->options->getLoginRedirectRoute();
                if ($user) {
                    $roleIdentifier = $user->getService()->getRole()->getRoleId();
                    if ($roleIdentifier == Role::SUPERADMIN) {
                        $loginRedirectRoute = 'superadmin_index_index';
                    }
                    if ($roleIdentifier == Role::ADMIN) {
                        $loginRedirectRoute = 'admin_index_index';
                    }
                    if ($roleIdentifier == Role::TEACHER) {
                        $loginRedirectRoute = 'teacher_index_index';
                    }
                    if ($roleIdentifier == Role::STUDENT) {
                        $loginRedirectRoute = 'student_index_index';
                    }
                }
                $route = ($redirect) ?: $loginRedirectRoute;
                return $this->router->assemble([], ['name' => $route]);
                break;
            case 'zfcuser/logout':
                $route = ($redirect) ?: $this->options->getLogoutRedirectRoute();
                return $this->router->assemble([], ['name' => $route]);
                break;
            default:
                return $this->router->assemble([], ['name' => 'zfcuser']);
        }
    }

    /**
     * @return Application  
     */
    protected function getApplication()
    {
        return $this->application;
    }
}
