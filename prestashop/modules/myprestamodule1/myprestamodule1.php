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
  }

  public function install()
  {
    if(!parent::install() ||
      !$this->registerHook("displayProductTab") ||
      !$this->registerHook("displayHeader")) {
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

  public function hookDisplayHeader($params)
  {
    return $this->context->controller->addCSS($this->_path.'/views/css/myprestamodule1.css', 'all');
  }

  public function hookDisplayProductTab($params)
  {
      $this->context->smarty->assign([
          "video" => true,
          "video_key" => "oavMtUWDBTM"
      ]

    );
    return $this->display(__FILE__, "display-product-tab.tpl");
  }
}