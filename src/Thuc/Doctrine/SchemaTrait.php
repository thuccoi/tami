<?php

namespace Thuc\Doctrine;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

trait SchemaTrait {

    /**
     * 
     * @ODM\Id
     */
    private $id;

    /**
     *
     * @ODM\Field(type="hash")
     */
    private $data;

    /**
     *
     * @ODM\Field(type="timestamp")
     */
    private $since;

    /**
     *
     * @ODM\Field(type="timestamp")
     */
    private $last_update;

    public function __construct() {
        $this->data = [];
        $this->since = new \MongoTimestamp(time());
        $this->last_update = new \MongoTimestamp(time());
    }

    public function export() {
        return (object) [
                    "id" => $this->id,
                    "data" => $this->getData(),
                    "last_update" => $this->getLastUpdate(),
                    "since" => $this->getSince()
        ];
    }

    public function setId(\MongoID $id) {
        $this->id = $id;
        return $this;
    }

    public function getId() {
        $this->id;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    public function getSince() {
        if (isset($this->since->sec)) {
            return $this->since->sec;
        } else {
            return 0;
        }
    }

    public function getLastUpdate() {
        if (isset($this->last_update->sec)) {
            return $this->last_update->sec;
        } else {
            return 0;
        }
    }

    public function setLastUpdate(\MongoTimestamp $last_update) {
        $this->last_update = $last_update;
        return $this;
    }

}
