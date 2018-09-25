# Cấu hình
Sao chép file `src/Thuc/bin/thuc.local.php.dist` và đổi tên vào trong cấu hình trong `config/autoload/thuc.local.php` của dự án
Thêm `'Thuc\Oauth' => 'thuc',` vào `config/autoload/module.doctrine-mongo-odm.local.php`

`
     'odm_default' => array(
                'drivers' => array(
                    //....
                    'Thuc\Oauth' => 'thuc',
                )
            ),
    'thuc' => array(
                'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    'Thuc\Oauth'
                )
            ),
`

thêm ENV vào onDispatch controller 

`    function onDispatch(MvcEvent $e) {
  //enviroment
        $this->layout()->ENV = $this->ENV;
}`

# add class Member to System\Model\Member 

    /**
     *
     * @ODM\ReferenceOne(targetDocument="\Thuc\Oauth\User", inversedBy="members")
     */
    private $user;