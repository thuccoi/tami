<?php

namespace Thuc\Zend;

final class Factory implements \Zend\ServiceManager\Factory\FactoryInterface {

    public function __invoke(\Interop\Container\ContainerInterface $services, $requestedName, array $options = null) {

        $config = $services->get('config');

        if (!isset($config["environment"]['ENV'])) {
            throw new \Exception('Please add the ENV in environment local.php');
        }

        $ENV = $config["environment"]['ENV'];

        $dm = $services->get('doctrine.documentmanager.odm_default');


        return new $requestedName($dm, $ENV);
    }

}
