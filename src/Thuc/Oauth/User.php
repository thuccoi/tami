<?php

namespace Thuc\Oauth;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable;

/**
 * 
 * @ODM\Document(db="oauth2", collection="oauth_users")
 */
class User implements SoftDeleteable {

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
    private $token;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $username;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $email;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $client_id;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $first_name;

    /**
     * @ODM\Field(type="string")
     */
    private $last_name;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $picture;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $password;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $phone;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $address;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $title;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $info;

    /**
     * 
     * @ODM\Field(type="bool")
     */
    private $public;

    /**
     * 
     * @ODM\Field(type="int")
     */
    private $status;

    /**
     * 
     * @ODM\Field(type="string")
     */
    private $create_from;

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
    public static $PUBLIC = true;
    public static $UNPUBLIC = false;
    public static $ACTIVE = 1;
    public static $INACTIVE = -1;
    public static $PICTURE_DEFAULT = "/img/avatar.png";
    public static $FROM_GOOGLE = "google";
    public static $FROM_NATIVE = "native";

    public function __construct($client_id = "") {
        $this->construct($client_id);
    }

    protected function construct($client_id = "") {
        $this->status = 0;

        if ($client_id) {
            $this->client_id = $client_id;
        }

        $this->public = FALSE;

        $this->create_from = static::$FROM_NATIVE;
        $this->picture = static::$PICTURE_DEFAULT;
        $this->data = [];
        $this->since = new \MongoTimeStamp();
        $this->last_update = new \MongoTimeStamp();
    }

    public function activate() {
        $this->status = self::$ACTIVE;
        return $this;
    }

    public function deactivate() {
        $this->status = self::$INACTIVE;
        return $this;
    }

    public function isActive() {
        return ($this->status === self::$ACTIVE);
    }

    public function getId() {
        return $this->id;
    }

    public function setId(\MongoId $id) {
        $this->id = $id;
        return $this;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function getClientID() {
        return $this->client_id;
    }

    public function setClientID($client_id = "") {

        $this->client_id = $client_id;
        return $this;
    }

    public function getToken() {
        return $this->token;
    }

    public function setToken($token) {
        $this->token = $token;
        return $this;
    }

    public function getCreateFrom() {
        return $this->create_from;
    }

    public function setCreateFrom($create_from) {

        if (!$create_from) {
            $create_from = self::$FROM_NATIVE;
        }

        if ($create_from == self::$FROM_GOOGLE) {
            $this->status = self::$ACTIVE;
        }

        $this->create_from = $create_from;

        return $this;
    }

    public function getPicture() {
        if (!$this->picture) {
            $this->picture = self::$pictureDefault;
        }

        return $this->picture;
    }

    public function setPicture($picture) {
        $this->picture = $picture;
        return $this;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = (int) $status;
        return $this;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $options = [
            'cost' => 11
        ];

        $this->password = password_hash($password, PASSWORD_BCRYPT, $options);

        return $this;
    }

    public function resetPassword($password) {

        $this->setPassword($password);

        $this->renewToken("reset_password");

        return $this;
    }

    public function getFirstName() {
        return $this->first_name;
    }

    public function setFirstName($first_name) {
        $this->first_name = $first_name;
        return $this;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function setLastName($last_name) {
        $this->last_name = $last_name;
        return $this;
    }

    public function getName() {
        return $this->last_name . " " . $this->first_name;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getInfo() {
        if ($this->info) {
            return base64_decode($this->info);
        }
        return '';
    }

    public function setInfo($info) {
        $this->info = $info;
        return $this;
    }

    public function getPublic() {
        return $this->public;
    }

    public function setPublic($public) {
        $this->public = $public;
        return $this;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    public function getSince() {
        return $this->since->sec;
    }

    public function getLastUpdate() {
        return $this->last_update->sec;
    }

    public function setLastUpdate(\MongoTimeStamp $last_update) {
        $this->last_update = $last_update;
        return $this;
    }

    public function export() {
        return (object) [
                    "id" => $this->getId(),
                    "client_id" => $this->getClientID(),
                    "username" => $this->getUsername(),
                    "email" => $this->getEmail(),
                    "token" => $this->getToken(),
                    "status" => $this->getStatus(),
                    "picture" => $this->getPicture(),
                    "name" => $this->getName(),
                    "phone" => $this->getPhone(),
                    "address" => $this->getAddress(),
                    "title" => $this->getTitle(),
                    "info" => $this->getInfo(),
                    "public" => $this->getPublic(),
                    "create_from" => $this->getCreateFrom(),
                    "data" => $this->getData(),
                    "last_update" => $this->getLastUpdate(),
                    "since" => $this->getSince(),
        ];
    }

    public function release() {
        $export = $this->export();
        return $export;
    }

}
