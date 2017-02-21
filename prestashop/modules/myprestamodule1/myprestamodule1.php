<?php
if (!defined('_PS_VERSION_')) {
  exit;
}

class MyPrestaModule1 extends Module
{
  public function __construct()
  {
    $this->name = 'myprestamodule1';
    $this->tab = 'front_office_features';
    $this->version = '1.0.0';
    $this->author = 'laidet_r & cherbi_r';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    $this->bootstrap = true;

    parent::__construct();

    $this->displayName = $this->l('My PrestaModule 1');
    $this->description = $this->l('A kick-ass first presta module to add a Youtube video on a product page.');

    $this->confirmUninstall = $this->l('Are you sure you want to uninstall ?!');

    if (!Configuration::get('MYMODULE_NAME')) {
      $this->warning = $this->l('No name provided');
    }
  }

  public function install()
  {
    if(!parent::install()) {
      return false;
    }
    return true;
  }

  public function uninstall()
  {
    if(!parent::uninstall()) {
      return false;
    }
    return true;
  }

  public function getContent()
  {

  }
}