<?php

namespace Thuc\Oauth;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable;

/**
 * 
 * @ODM\Document(collection="oauth_refresh_tokens")
 */
class RefreshToken implements SoftDeleteable {

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
    private $refresh_token;

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

    public function getRefreshToken() {
        return $this->refresh_token;
    }

    public function getClientId() {
        return $this->client_id;
    }

    public function getUserId() {
        return $this->user_id;
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
                    "refresh_token" => $this->getRefreshToken(),
                    "client_id" => $this->getClientId(),
                    "user_id" => $this->getUserId(),
                    "expires" => $this->getExpires(),
                    "scope" => $this->getScope()
        ];
    }

}
