<?php

namespace Thuc\Doctrine;

use Doctrine\ODM\MongoDB\SoftDelete\Configuration;
use Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteManager;
use Doctrine\Common\EventManager;

abstract class RestFul {

    public $dm;
    public $classname;
    public $cascading;

    public function __construct($dm, $classname, $CascadingSoftDeleteListener) {
        $this->dm = $dm;
        $this->classname = $classname;
        $this->cascading = $CascadingSoftDeleteListener;
    }

    public function create($data) {
        try {
            $obj = new $this->classname();
            foreach ($data as $key => $val) {
                $obj->{"set$key"}($val);
            }
            $this->dm->persist($obj);
            $this->dm->flush($obj);
            $this->dm->clear();
            return true;
        } catch (\Doctrine\MongoDB\Exception $ex) {
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $obj = $this->findOneBy(["id" => $id]);
            if ($obj) {
                foreach ($data as $key => $val) {
                    $obj->{"set$key"}($val);
                }
                $update_at = new \MongoTimeStamp();
                $obj->setLastUpdate($update_at);
                $this->dm->persist($obj);
                $this->dm->flush($obj);
                $this->dm->clear();
                return true;
            }
            return false;
        } catch (\Doctrine\MongoDB\Exception $ex) {
            return false;
        }
    }

    abstract protected function getOne($arr);

    abstract protected function getMany($arr);

    public function delete($id) {

        $find = $this->dm->getRepository($this->classname)->find($id);
        if (!$find) {
            return false;
        }

        try {

            $config = new Configuration();
            $evm = new EventManager();
            $eventSubscriber = new $this->cascading();
            $evm->addEventSubscriber($eventSubscriber);
            $sdm = new SoftDeleteManager($this->dm, $config, $evm);

            $sdm->delete($find);

            $sdm->flush();


            return true;
        } catch (\Doctrine\MongoDB\Exception $ex) {
            return false;
        }
    }

    public function restore($id) {

        $find = $this->dm->getRepository($this->classname)->find($id);
        if (!$find) {
            return FALSE;
        }

        if ($find) {
            $config = new Configuration();
            $evm = new EventManager();
            $eventSubscriber = new $this->cascading();
            $evm->addEventSubscriber($eventSubscriber);
            $sdm = new SoftDeleteManager($this->dm, $config, $evm);

            $sdm->restore($find);

            $sdm->flush();

            return TRUE;
        }

        return FALSE;
    }

}
