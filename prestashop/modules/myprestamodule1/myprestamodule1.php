<?php
if (!defined('_PS_VERSION_')) {
  exit;
}

require_once __DIR__ . '/models/Video.php';

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
      !$this->addTables() ||
      !$this->registerHook("displayProductTab") ||
      !$this->registerHook("displayHeader")) {
      return false;
    }
    return true;
  }

  public function uninstall()
  {
    if(!parent::uninstall() ||
      !$this->removeTables()) {
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
    $id_product = $params["product"]->specificPrice["id_product"];

    $video = Video::findByProductId($id_product);
    $this->context->smarty->assign("video", $video);
    return $this->display(__FILE__, "display-product-tab.tpl");
  }

  public function addTables()
  {
    $sql = [];
    array_push($sql, require (__DIR__ . '/sql/video.php'));
    foreach ($sql as $s) {
      if (!Db::getInstance()->execute($s)) {
        return false;
      }
    }
    return true;
  }

  public function removeTables()
  {
    $sql = [];
    array_push($sql, require (__DIR__ . '/sql/video.reverse.php'));
    foreach ($sql as $s) {
      if (!Db::getInstance()->execute($s)) {
        return false;
      }
    }
    return true;
  }
}