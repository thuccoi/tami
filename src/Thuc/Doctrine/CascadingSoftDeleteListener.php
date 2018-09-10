<?php

namespace Thuc\Doctrine;

use Doctrine\ODM\MongoDB\SoftDelete\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\SoftDelete\Events;
use Doctrine\Common\EventSubscriber;

abstract class CascadingSoftDeleteListener implements EventSubscriber {

    public function preSoftDelete(LifecycleEventArgs $args) {
        $sdm = $args->getSoftDeleteManager();
        $document = $args->getDocument();
//      if ($document instanceof \Backend\Model\NhomNhuCau) {
//            $sdm->deleteBy('Backend\Model\NhuCau', array('group.id' => $document->getId()));
//            ...
//      }
        $this->eachDocument($sdm, $document, "delete");
    }

    public function preRestore(LifecycleEventArgs $args) {
        $sdm = $args->getSoftDeleteManager();
        $document = $args->getDocument();

//      if ($document instanceof \Backend\Model\NhomNhuCau) {
//            $sdm->restoreBy('Backend\Model\NhuCau', array('group.id' => $document->getId()));
//            ...
//      }

        $this->eachDocument($sdm, $document, "restore");
    }

    abstract protected function eachDocument($sdm, $document);

    public function getSubscribedEvents() {
        return array(
            Events::preSoftDelete,
            Events::preRestore
        );
    }

}
