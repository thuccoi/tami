<?php

namespace Backend\Model;

use Doctrine\ODM\MongoDB\SoftDelete\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\SoftDelete\Events;
use Doctrine\Common\EventSubscriber;

class CascadingSoftDeleteListener implements EventSubscriber {

    public function preSoftDelete(LifecycleEventArgs $args) {
        $sdm = $args->getSoftDeleteManager();
        $document = $args->getDocument();

        if ($document instanceof \Backend\Model\NhomNhuCau) {
            $sdm->deleteBy('Backend\Model\NhuCau', array('group.id' => $document->getId()));
        }

        if ($document instanceof \Backend\Model\NhuCau) {
            $sdm->deleteBy('Backend\Model\SanXuat', array('nhucau.id' => $document->getId()));
            $sdm->deleteBy('Backend\Model\TieuThu', array('nhucau.id' => $document->getId()));
        }


        if ($document instanceof \Backend\Model\KhuVuc) {
            $sdm->deleteBy('Backend\Model\DiaDiem', array('khuvuc.id' => $document->getId()));
        }

        if ($document instanceof \Backend\Model\DiaDiem) {
            $sdm->deleteBy('Backend\Model\SanXuat', array('diadiem.id' => $document->getId()));
            $sdm->deleteBy('Backend\Model\TieuThu', array('diadiem.id' => $document->getId()));
        }


        if ($document instanceof \Backend\Model\NhomTieuThu) {
            $sdm->deleteBy('Backend\Model\TieuThu', array('nhomtieuthu.id' => $document->getId()));
        }


        if ($document instanceof \Backend\Model\TieuThu) {
            $sdm->deleteBy('Backend\Model\SucMua', array('tieuthu.id' => $document->getId()));
        }


        if ($document instanceof \Backend\Model\NhaSanXuat) {
            $sdm->deleteBy('Backend\Model\SanXuat', array('nhasanxuat.id' => $document->getId()));
        }

        if ($document instanceof \Backend\Model\SanXuat) {
            $sdm->deleteBy('Backend\Model\SanLuong', array('sanxuat.id' => $document->getId()));
        }
    }

    public function preRestore(LifecycleEventArgs $args) {
        $sdm = $args->getSoftDeleteManager();
        $document = $args->getDocument();

         if ($document instanceof \Backend\Model\NhomNhuCau) {
            $sdm->restoreBy('Backend\Model\NhuCau', array('group.id' => $document->getId()));
        }

        if ($document instanceof \Backend\Model\NhuCau) {
            $sdm->restoreBy('Backend\Model\SanXuat', array('nhucau.id' => $document->getId()));
            $sdm->restoreBy('Backend\Model\TieuThu', array('nhucau.id' => $document->getId()));
        }


        if ($document instanceof \Backend\Model\KhuVuc) {
            $sdm->restoreBy('Backend\Model\DiaDiem', array('khuvuc.id' => $document->getId()));
        }

        if ($document instanceof \Backend\Model\DiaDiem) {
            $sdm->restoreBy('Backend\Model\SanXuat', array('diadiem.id' => $document->getId()));
            $sdm->restoreBy('Backend\Model\TieuThu', array('diadiem.id' => $document->getId()));
        }


        if ($document instanceof \Backend\Model\NhomTieuThu) {
            $sdm->restoreBy('Backend\Model\TieuThu', array('nhomtieuthu.id' => $document->getId()));
        }


        if ($document instanceof \Backend\Model\TieuThu) {
            $sdm->restoreBy('Backend\Model\SucMua', array('tieuthu.id' => $document->getId()));
        }


        if ($document instanceof \Backend\Model\NhaSanXuat) {
            $sdm->restoreBy('Backend\Model\SanXuat', array('nhasanxuat.id' => $document->getId()));
        }

        if ($document instanceof \Backend\Model\SanXuat) {
            $sdm->restoreBy('Backend\Model\SanLuong', array('sanxuat.id' => $document->getId()));
        }
    }

    public function getSubscribedEvents() {
        return array(
            Events::preSoftDelete,
            Events::preRestore
        );
    }

}
