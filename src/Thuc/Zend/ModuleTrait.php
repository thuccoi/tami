<?php

namespace Thuc\Zend;

use Zend\Mvc\MvcEvent;
use Zend\Session\SessionManager;
use Zend\Session\Container;

trait ModuleTrait {

    public function onBootstrap(MvcEvent $event) {
        $this->bootstrap($event);
    }

    public function bootstrap(MvcEvent $event) {
        $application = $event->getApplication();
        $serviceManager = $application->getServiceManager();
        // The following line instantiates the SessionManager and automatically
        // makes the SessionManager the 'default' one.
        $sessionManager = $serviceManager->get(SessionManager::class);

        // We assume that $sessionManager variable is an instance of the session manager.
        $sessionContainer = new Container('ContainerNamespace', $sessionManager);

        $event->setParam('sessionContainer', $sessionContainer);

//        if (!isset($sessionContainer->myVar)) {
//            $sessionContainer->myVar = 'Some data';
////            unset($sessionContainer->myVar);
//        }
//        if (isset($sessionContainer->myVar))
//            $myVar = $sessionContainer->myVar;
//        else
//            $myVar = null;
//        unset($sessionContainer->myVar);
    }

}
