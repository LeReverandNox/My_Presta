<?php
if (!defined('_PS_VERSION_')) {
  exit;
}

require_once __DIR__ . '/lib/twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth as TwitterOAuth;

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
      !$this->registerHook("actionProductAdd") ||
      ! Configuration::updateValue('MYPRESTAMODULE2_CUSTOM_TWEET', 'is now available on our store ! Check it out !')) {
      return false;
    }
    return true;
  }

  public function uninstall()
  {
    if(!parent::uninstall() ||
      !Configuraton::deleteByName('MYPRESTAMODULE2_CUSTOM_TWEET')) {
      return false;
    }
    return true;
  }
}