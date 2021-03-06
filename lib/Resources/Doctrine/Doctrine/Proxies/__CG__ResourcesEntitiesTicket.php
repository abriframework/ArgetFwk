<?php

namespace Proxies\__CG__\Resources\Entities;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Ticket extends \Resources\Entities\Ticket implements \Doctrine\ORM\Proxy\Proxy {
  private $_entityPersister;
  private $_identifier;
  public $__isInitialized__ = false;

  public function __construct($entityPersister, $identifier) {
    $this->_entityPersister = $entityPersister;
    $this->_identifier      = $identifier;
  }

  /** @private */
  public function __load() {
    if (!$this->__isInitialized__ && $this->_entityPersister) {
      $this->__isInitialized__ = true;

      if (method_exists($this, "__wakeup")) {
        // call this after __isInitialized__to avoid infinite recursion
        // but before loading to emulate what ClassMetadata::newInstance()
        // provides.
        $this->__wakeup();
      }

      if ($this->_entityPersister->load($this->_identifier, $this) === null) {
        throw new \Doctrine\ORM\EntityNotFoundException();
      }
      unset($this->_entityPersister, $this->_identifier);
    }
  }

  /** @private */
  public function __isInitialized() {
    return $this->__isInitialized__;
  }


  public function getId() {
    if ($this->__isInitialized__ === false) {
      return (int)$this->_identifier["id"];
    }
    $this->__load();

    return parent::getId();
  }

  public function setId($id) {
    $this->__load();

    return parent::setId($id);
  }

  public function getAdmin() {
    $this->__load();

    return parent::getAdmin();
  }

  public function setAdmin($admin) {
    $this->__load();

    return parent::setAdmin($admin);
  }

  public function getTypeTicket() {
    $this->__load();

    return parent::getTypeTicket();
  }

  public function getTypeTicketPerso() {
    $this->__load();

    return parent::getTypeTicketPerso();
  }

  public function setTypeTicket($typeTicket) {
    $this->__load();

    return parent::setTypeTicket($typeTicket);
  }

  public function getStatut() {
    $this->__load();

    return parent::getStatut();
  }

  public function setStatut($statut) {
    $this->__load();

    return parent::setStatut($statut);
  }

  public function getDate() {
    $this->__load();

    return parent::getDate();
  }

  public function setDate($date) {
    $this->__load();

    return parent::setDate($date);
  }

  public function getTitre() {
    $this->__load();

    return parent::getTitre();
  }

  public function setTitre($titre) {
    $this->__load();

    return parent::setTitre($titre);
  }

  public function getTexte() {
    $this->__load();

    return parent::getTexte();
  }

  public function getTexteHtml() {
    $this->__load();

    return parent::getTexteHtml();
  }

  public function setTexte($texte) {
    $this->__load();

    return parent::setTexte($texte);
  }


  public function __sleep() {
    return array('__isInitialized__', 'id', 'typeTicket', 'statut', 'date', 'titre', 'texte', 'admin');
  }

  public function __clone() {
    if (!$this->__isInitialized__ && $this->_entityPersister) {
      $this->__isInitialized__ = true;
      $class                   = $this->_entityPersister->getClassMetadata();
      $original                = $this->_entityPersister->load($this->_identifier);
      if ($original === null) {
        throw new \Doctrine\ORM\EntityNotFoundException();
      }
      foreach ($class->reflFields AS $field => $reflProperty) {
        $reflProperty->setValue($this, $reflProperty->getValue($original));
      }
      unset($this->_entityPersister, $this->_identifier);
    }

  }
}