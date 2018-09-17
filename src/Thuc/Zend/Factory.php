<?php

namespace Thuc\Zend;

use Zend\Session\SessionManager;
use Zend\Session\Container;

final class Factory implements \Zend\ServiceManager\Factory\FactoryInterface {

    public function __invoke(\Interop\Container\ContainerInterface $services, $requestedName, array $options = null) {

        $config = $services->get('config');

        if (!isset($config["environment"]['ENV'])) {
            throw new \Exception('Please add the ENV in environment local.php');
        }

        $ENV = $config["environment"]['ENV'];

        $dm = $services->get('doctrine.documentmanager.odm_default');

        $sessionManager = $services->get(SessionManager::class);
        $sessionContainer = new Container('thuc', $sessionManager);

        return new $requestedName($dm, $ENV, $sessionContainer);
    }

}
