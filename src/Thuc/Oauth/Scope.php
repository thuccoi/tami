<?php

namespace Thuc\Oauth;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable;

/**
 * 
 * @ODM\Document(database="oauth2", collection="oauth_scopes")
 */
class Scope implements SoftDeleteable {

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
     * @ODM\Field(type="string")
     */
    private $type = "supported";

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $client_id;

    /**
     * 
     * @ODM\Field(type="int")
     */
    private $is_default;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $scope;

    public function getId() {
        return $this->id;
    }

    public function getType() {
        return $this->type;
    }

    public function getClientId() {
        return $this->client_id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getIsDefault() {
        return $this->is_default;
    }

    public function getScope() {
        return $this->scope;
    }

    public function release() {
        return (object) [
                    "id" => $this->getId(),
                    "type" => $this->getType(),
                    "client_id" => $this->getClientId(),
                    "user_id" => $this->getUserId(),
                    "is_default" => $this->getIsDefault(),
                    "scope" => $this->getScope()
        ];
    }

}
