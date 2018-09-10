<?php

namespace Thuc\Doctrine;

use Doctrine\ODM\MongoDB\SoftDelete\Configuration;
use Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteManager;
use Doctrine\Common\EventManager;

class RestFul {

    private $dm;
    private $classname;
    private $CascadingSoftDeleteListener;

    public function __construct($dm, $classname, $CascadingSoftDeleteListener) {
        $this->dm = $dm;
        $this->classname = $classname;
        $this->CascadingSoftDeleteListener = $CascadingSoftDeleteListener;
    }

    public function create($data) {
        try {
            $obj = new $this->classname();
            foreach ($data as $key => $val) {
                $obj->{"set$key"}($val);
            }
            $create_at = new \MongoTimeStamp();
            $obj->setSince($create_at);
            $this->dm->persist($obj);
            $this->dm->flush($obj);
            $this->dm->clear();
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
            }
        } catch (\Doctrine\MongoDB\Exception $ex) {
            return false;
        }
    }

    public function findBy($arr) {
        try {
            $arr[] = ["deletedAt" => null];
            return $this->dm->getRepository($this->classname)->findBy($arr);
        } catch (\Doctrine\MongoDB\Exception $ex) {
            return [];
        }
    }

    public function findOneBy($arr) {
        try {
            $arr[] = ["deletedAt" => null];
            return $this->dm->getRepository($this->cascname)->findOneBy($arr);
        } catch (\Doctrine\MongoDB\Exception $ex) {
            return null;
        }
    }

    public function delete($id) {

        $find = $this->dm->getRepository($this->classname)->find($id);
        if (!$find) {
            return false;
        }

        try {

            $config = new Configuration();
            $evm = new EventManager();
            $eventSubscriber = new $this->CascadingSoftDeleteListener();
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
            $eventSubscriber = new $this->CascadingSoftDeleteListener();
            $evm->addEventSubscriber($eventSubscriber);
            $sdm = new SoftDeleteManager($this->dm, $config, $evm);

            $sdm->restore($find);

            $sdm->flush();

            return TRUE;
        }

        return FALSE;
    }

}
