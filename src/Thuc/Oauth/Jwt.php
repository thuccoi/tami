<?php

namespace Thuc\Oauth;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable;

/**
 * 
 * @ODM\Document(db="oauth2", collection="oauth_jwt")
 */
class Jwt implements SoftDeleteable {

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
    private $subject;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $client_id;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $public_key;

    public function getId() {
        return $this->id;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function getClientId() {
        return $this->client_id;
    }

    public function getPublicKey() {
        return $this->public_key;
    }

    public function release() {
        return (object) [
                    "id" => $this->getId(),
                    "subject" => $this->getSubject(),
                    "client_id" => $this->getClientId(),
                    "public_key" => $this->getPublicKey()
        ];
    }

}
