<?php

return array(
    'doctrine' => array(
        'connection'      => array(
            'odm_default' => array(
                'server'  => '172.17.0.1',
                'port'    => '27017',
                'dbname'  => 'fbc',
                'options' => array()
            ),
        ),
        'configuration'   => array(
            'odm_default' => array(
                'metadata_cache'     => 'array',
                'driver'             => 'odm_default',
                'generate_proxies'   => true,
                'proxy_dir'          => 'data/DoctrineMongoODMModule/Proxy',
                'proxy_namespace'    => 'DoctrineMongoODMModule\Proxy',
                'generate_hydrators' => true,
                'hydrator_dir'       => 'data/DoctrineMongoODMModule/Hydrator',
                'hydrator_namespace' => 'DoctrineMongoODMModule\Hydrator',
                'default_db'         => 'fbc',
                'filters'            => array()
            ),
        ),
        'driver'          => array(
            'odm_default' => array(
                'drivers' => array(
                    'Backend\Model' => 'ami',
                )
            ),
            'ami'         => array(
                'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    'Backend\Model'
                )
            ),
        ),
        'documentmanager' => array(
            'odm_default' => array(
                'connection'    => 'odm_default',
                'configuration' => 'odm_default',
                'eventmanager'  => 'odm_default'
            )
        ),
        'eventmanager'    => array(
            'odm_default' => array(
                'subscribers' => array()
            )
        ),
    ),
);
