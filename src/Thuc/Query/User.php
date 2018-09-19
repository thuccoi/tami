<?php

namespace Thuc\Query;

class User extends \Thuc\Doctrine\RestFul {

    public function __construct($dm) {
        $classname = '\Thuc\Oauth\User';
        $CascadingSoftDeleteListener = '\Thuc\Doctrine\CascadingSoftDeleteListener';

        parent::__construct($dm, $classname, $CascadingSoftDeleteListener);
    }

    public function create($data) {
        try {

            if (!isset($data["client_id"])) {
                return (object) [
                            "status" => 403,
                            "message" => "Lỗi bảo mật"
                ];
            }

            if (!isset($data["email"])) {
                return (object) [
                            "status" => 403,
                            "message" => "Yêu cầu địa chỉ Email"
                ];
            }

            if (!isset($data["password"])) {
                return (object) [
                            "status" => 403,
                            "message" => "Yêu cầu mật khẩu"
                ];
            }

            if (!isset($data["first_name"])) {
                return (object) [
                            "status" => 403,
                            "message" => "Yêu cầu tên"
                ];
            }

            if (!isset($data["last_name"])) {
                return (object) [
                            "status" => 403,
                            "message" => "Yêu cầu họ"
                ];
            }

            if (!isset($data["phone"])) {
                return (object) [
                            "status" => 403,
                            "message" => "Yêu cầu số điện thoại"
                ];
            }

            $obj = new $this->classname();

            $obj->setClientID($data["client_id"]);

            if (isset($data["token"])) {
                $obj->setToken($data["token"]);
            }


            if (isset($data["first_name"])) {
                $obj->setFirstName($data["first_name"]);
            }

            if (isset($data["last_name"])) {
                $obj->setLastName($data["last_name"]);
            }

            if (isset($data["phone"]) && $data["phone"]) {
                $find = $this->dm->getRepository($this->classname)->findOneBy(["phone" => $data["phone"]]);
                if ($find) {
                    return (object) [
                                "status" => 403,
                                "message" => "Số điện thoại đã tồn tại trong hệ thống"
                    ];
                }

                $obj->setPhone($data["phone"]);
            }


            if (isset($data["email"]) && $data["email"]) {
                if ($this->getOne($data["email"])) {
                    return (object) [
                                "status" => 403,
                                "message" => "Email đã tồn tại trong hệ thống"
                    ];
                }

                $obj->setUsername($data["email"]);
                $obj->setEmail($data["email"]);
            }

            if (isset($data["password"])) {
                $obj->setPassword($data["password"]);
            }

            $this->dm->persist($obj);
            $this->dm->flush();
            $this->dm->clear();

            return (object) [
                        "status" => 200,
                        "message" => "Tạo tài khoản thành công"
            ];
        } catch (\Doctrine\MongoDB\Exception $ex) {
            return (object) [
                        "status" => 500,
                        "message" => "Đã có lỗi xảy ra với máy chủ"
            ];
        }
    }

    public function update($id, $data) {
        ;
    }

    public function getMany($arr) {
        ;
    }

    public function getOne($id) {
        //id
        $find = $this->dm->getRepository($this->classname)->find($id);
        //email
        if (!$find) {
            $find = $this->dm->getRepository($this->classname)->findOneBy(["email" => $id]);
        }
        //username
        if (!$find) {
            $find = $this->dm->getRepository($this->classname)->findOneBy(["username" => $id]);
        }


        return $find;
    }

}
