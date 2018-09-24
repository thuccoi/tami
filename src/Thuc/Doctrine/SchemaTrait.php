<?php

namespace Thuc\Doctrine;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

trait SchemaTrait {

    /** @ODM\Field(type="date") */
    private $deletedAt;

    public function getDeletedAt() {
        return $this->deletedAt;
    }

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
        $this->construct();
    }

    protected function construct() {
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

    public function updateData($key, $value) {

        if ($this->data && isset($this->data[$key])) {
            $this->data[$key] = $value;
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    public function getDataByKey($key) {

        if ($this->data && isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
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
