<?php
if (!defined('_PS_VERSION_')) {
  exit;
}

class MyPrestaModule1 extends Module
{
  public function __construct()
  {
    $this->name = 'myprestamodule1';
    $this->tab = 'social_networks';
    $this->version = '1.0.0';
    $this->author = 'Rodolphe Laidet';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    $this->bootstrap = true;

    parent::__construct();

    $this->displayName = $this->l('My PrestaModule 1');
    $this->description = $this->l('A kick-ass first presta module.');

    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

    if (!Configuration::get('MYMODULE_NAME')) {
      $this->warning = $this->l('No name provided');
    }
  }

  public function install()
  {
    if(!parent::install() ||
      !Configuration::updateValue("MY_PRESTAMODULE_1_NAME", "toto")) {
      return false;
    }
    return true;
  }

  public function uninstall()
  {
    if(!parent::uninstall() ||
      !Configuration::deleteByName("MY_PRESTAMODULE_1_NAME")) {
      return false;
    }
    return true;
  }

  public function getContent()
  {

  }
}