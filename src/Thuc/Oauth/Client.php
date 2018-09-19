<?php

namespace Thuc\Oauth;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable;

/**
 * 
 * @ODM\Document(database="oauth2", collection="oauth_clients")
 */
class Client implements SoftDeleteable {

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
    private $client_id;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $client_secret;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $redirect_uri;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $grant_types;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $scope;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $user_id;

    public function getId() {
        return $this->id;
    }

    public function getClientSecret() {
        return $this->client_secret;
    }

    public function getClientId() {
        return $this->client_id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getRedirectUri() {
        return $this->redirect_uri;
    }

    public function getGrantTypes() {
        return $this->grant_types;
    }

    public function getScope() {
        return $this->scope;
    }

    public function release() {
        return (object) [
                    "id" => $this->getId(),
                    "client_secret" => $this->getClientSecret(),
                    "client_id" => $this->getClientId(),
                    "user_id" => $this->getUserId(),
                    "redirect_uri" => $this->getRedirectUri(),
                    "grant_types" => $this->getGrantTypes(),
                    "scope" => $this->getScope()
        ];
    }

}
