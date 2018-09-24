<?php

namespace Thuc\Query;

class User extends \Thuc\Doctrine\RestFul {

    public function __construct($dm) {
        $classname = '\Thuc\Oauth\User';
        $CascadingSoftDeleteListener = '\Thuc\Doctrine\CascadingSoftDeleteListener';

        parent::__construct($dm, $classname, $CascadingSoftDeleteListener);
    }

    public function setPassword($data) {
        try {
            if (!$data || !isset($data["email"]) || !isset($data["token"]) || !isset($data["password"])) {
                return (object) [
                            "status" => 403,
                            "message" => "Không đúng định dạng dữ liệu"
                ];
            }

            $user = $this->getOne($data["email"]);
            if (!$user) {
                return (object) [
                            "status" => 404,
                            "message" => "Không tồn tại tài khoản này trong hệ thống"
                ];
            }

            if (!$user->isActive()) {
                return (object) [
                            "status" => 403,
                            "message" => "Tài khoản này chưa được kích hoạt"
                ];
            }

            if ($user->getToken() != $data["token"]) {
                return (object) [
                            "status" => 403,
                            "message" => "Liên kết đã hết hạn, hoặc bị lỗi"
                ];
            }

            $user->setPassword($data["password"])->setLastUpdate(new \MongoTimeStamp());

            $this->dm->persist($user);
            $this->dm->flush();
            $this->dm->clear();

            return (object) [
                        "status" => 200,
                        "message" => "Đặt mật khẩu thành công"
            ];
        } catch (\Doctrine\MongoDB\Exception $ex) {
            return (object) [
                        "status" => 405,
                        "message" => "Lỗi máy chủ"
            ];
        }

        return (object) [
                    "status" => 405,
                    "message" => "Lỗi máy chủ"
        ];
    }

    public function checkToken($data) {
        try {
            if (!$data || !isset($data["email"]) || !isset($data["token"])) {
                return (object) [
                            "status" => 403,
                            "message" => "Không đúng định dạng dữ liệu"
                ];
            }

            $user = $this->getOne($data["email"]);
            if (!$user) {
                return (object) [
                            "status" => 404,
                            "message" => "Không tồn tại tài khoản này trong hệ thống"
                ];
            }

            if (!$user->isActive()) {
                return (object) [
                            "status" => 403,
                            "message" => "Tài khoản này chưa được kích hoạt"
                ];
            }

            if ($user->getToken() != $data["token"]) {
                return (object) [
                            "status" => 403,
                            "message" => "Liên kết đã hết hạn, hoặc bị lỗi"
                ];
            }

            return (object) [
                        "status" => 200,
                        "message" => "Khóa xác thực đúng"
            ];
        } catch (\Doctrine\MongoDB\Exception $ex) {
            return (object) [
                        "status" => 405,
                        "message" => "Lỗi máy chủ"
            ];
        }

        return (object) [
                    "status" => 405,
                    "message" => "Lỗi máy chủ"
        ];
    }

    public function activate($data) {
        try {
            if (!$data || !isset($data["email"]) || !isset($data["token"])) {
                return (object) [
                            "status" => 403,
                            "message" => "Không đúng định dạng dữ liệu"
                ];
            }

            $user = $this->getOne($data["email"]);
            if (!$user) {
                return (object) [
                            "status" => 404,
                            "message" => "Không tồn tại tài khoản này trong hệ thống"
                ];
            }

            if ($user->isActive()) {
                return (object) [
                            "status" => 403,
                            "message" => "Tài khoản này đang hoạt động, vì đã được kích hoạt từ trước đó"
                ];
            }

            if ($user->getToken() != $data["token"]) {
                return (object) [
                            "status" => 403,
                            "message" => "Liên kết đã hết hạn, hoặc bị lỗi"
                ];
            }

            //activate user
            $user->activate()->setLastUpdate(new \MongoTimeStamp());

            $this->dm->persist($user);
            $this->dm->flush();
            $this->dm->clear();
            return (object) [
                        "status" => 200,
                        "message" => "Kích hoạt tài khoản thành công"
            ];
        } catch (\Doctrine\MongoDB\Exception $ex) {
            return (object) [
                        "status" => 405,
                        "message" => "Lỗi máy chủ"
            ];
        }

        return (object) [
                    "status" => 405,
                    "message" => "Lỗi máy chủ"
        ];
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


            $obj = new $this->classname();

            $obj->setClientID($data["client_id"]);

            if (isset($data["token"])) {
                $obj->setToken($data["token"]);
            }


            if (isset($data["first_name"])) {
                $obj->setFirstName($data["first_name"]);
            }

            if (isset($data["picture"])) {
                $obj->setPicture($data["picture"]);
            }

            if (isset($data["create_from"])) {
                $obj->setCreateFrom($data["create_from"]);
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
        try {
            $user = $this->getOne($id);
            if ($user) {
                $hasupdate = false;
                if (isset($data["first_name"])) {
                    $user->setFirstName($data["first_name"]);
                    $hasupdate = true;
                }
                if (isset($data["last_name"])) {
                    $user->setLastName($data["last_name"]);
                    $hasupdate = true;
                }
                if (isset($data["title"])) {
                    $user->setTitle($data["title"]);
                    $hasupdate = true;
                }
                if (isset($data["info"])) {
                    $user->setInfo($data["info"]);
                    $hasupdate = true;
                }
                if (isset($data["address"])) {
                    $user->setAddress($data["address"]);
                    $hasupdate = true;
                }
                if (isset($data["phone"])) {
                    $user->setPhone($data["phone"]);
                    $hasupdate = true;
                }
                if (isset($data["mobile"])) {
                    $user->updateData("mobile", $data["mobile"]);
                    $hasupdate = true;
                }
                if (isset($data["fax"])) {
                    $user->updateData("fax", $data["fax"]);
                    $hasupdate = true;
                }
                if (isset($data["skype"])) {
                    $user->updateData("skype", $data["skype"]);
                    $hasupdate = true;
                }
                if (isset($data["facebook"])) {
                    $user->updateData("facebook", $data["facebook"]);
                    $hasupdate = true;
                }
                if (isset($data["twitter"])) {
                    $user->updateData("twitter", $data["twitter"]);
                    $hasupdate = true;
                }
                if (isset($data["google_plus"])) {
                    $user->updateData("google_plus", $data["google_plus"]);
                    $hasupdate = true;
                }
                if (isset($data["linkedin"])) {
                    $user->updateData("linkedin", $data["linkedin"]);
                    $hasupdate = true;
                }
                if (isset($data["pinterest"])) {
                    $user->updateData("pinterest", $data["pinterest"]);
                    $hasupdate = true;
                }
                if (isset($data["instagram"])) {
                    $user->updateData("instagram", $data["instagram"]);
                    $hasupdate = true;
                }
                if (isset($data["password"])) {
                    $user->setPassword($data["password"]);
                    $hasupdate = true;
                }
                if (isset($data["public"])) {

                    if ($data["public"] === "on" || $data["public"]) {
                        $user->setPublic(\Thuc\Oauth\User::$PUBLIC);
                    } else {
                        $user->setPublic(\Thuc\Oauth\User::$UNPUBLIC);
                    }

                    $hasupdate = true;
                }
                if ($hasupdate) {
                    $user->setLastUpdate(new \MongoTimestamp());
                    $this->dm->persist($user);
                    $this->dm->flush();
                    $this->dm->clear();

                    return true;
                }
            }
        } catch (\Doctrine\MongoDB\Exception $ex) {
            return false;
        }
        return false;
    }

    public function getMany($arr) {
        ;
    }

    public function getOne($id) {
        try {
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
        } catch (\Doctrine\MongoDB\Exception $ex) {
            return null;
        }
        return null;
    }

}
