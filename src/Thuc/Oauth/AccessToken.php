<?php

namespace Thuc\Oauth;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable;

/**
 * 
 * @ODM\Document(db="oauth2", collection="oauth_access_tokens")
 */
class AccessToken implements SoftDeleteable {

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
    private $access_token;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $client_id;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $user_id;

    /**
     * 
     * @ODM\Field(type="timestamp")
     */
    private $expires;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $scope;

    public function getId() {
        return $this->id;
    }

    public function getAccessToken() {
        return $this->access_token;
    }

    public function getClientId() {
        return $this->client_id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
        return $this;
    }

    public function getExpires() {
        return $this->expires;
    }

    public function getScope() {
        return $this->scope;
    }

    public function release() {
        return (object) [
                    "id" => $this->getId(),
                    "access_token" => $this->getAccessToken(),
                    "client_id" => $this->getClientId(),
                    "user_id" => $this->getUserId(),
                    "expires" => $this->getExpires(),
                    "scope" => $this->getScope()
        ];
    }

}
