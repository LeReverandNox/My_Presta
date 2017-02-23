<?php
if (!defined('_PS_VERSION_')) {
  exit;
}

class MyPrestaModule2 extends Module
{
  public function __construct()
  {
    $this->name = 'myprestamodule2';
    $this->tab = 'social_networks';
    $this->version = '1.0.0';
    $this->author = 'laidet_r & cherbi_r';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    $this->bootstrap = true;

    parent::__construct();

    $this->displayName = $this->l('My PrestaModule 2');
    $this->description = $this->l('Post a tweet when you add a product to your catalog.');

    $this->confirmUninstall = $this->l('Are you sure you want to uninstall ?!');
  }

  public function install()
  {
    if(!parent::install() ||
      !$this->registerHook("actionProductAdd")) {
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
}