<?php

namespace Thuc\Oauth;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable;

/**
 * 
 * @ODM\Document(database="oauth2", collection="oauth_authorization_codes")
 */
class AuthorizationCode implements SoftDeleteable {

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
    private $authorization_code;

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
     * @ODM\Field(type="string")
     */
    private $redirect_uri;

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

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $id_token;

    public function getId() {
        return $this->id;
    }

    public function getAuthorizationCode() {
        return $this->authorization_code;
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

    public function getExpires() {
        return $this->expires;
    }

    public function getScope() {
        return $this->scope;
    }

    public function getIdToken() {
        return $this->id_token;
    }

    public function release() {
        return (object) [
                    "id" => $this->getId(),
                    "authorization_code" => $this->getAuthorizationCode(),
                    "client_id" => $this->getClientId(),
                    "user_id" => $this->getUserId(),
                    "expires" => $this->getExpires(),
                    "redirect_uri" => $this->getRedirectUri(),
                    "scope" => $this->getScope(),
                    "id_token" => $this->getIdToken()
        ];
    }

}
