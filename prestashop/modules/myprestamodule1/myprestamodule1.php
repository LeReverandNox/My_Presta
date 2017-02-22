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
      !$this->registerHook("displayAdminProductsExtra") ||
      !$this->registerHook("actionProductUpdate") ||
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
    $id_product = (int)Tools::getValue('id_product');

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

  public function hookDisplayAdminProductsExtra($params)
  {
    $id_product = (int)Tools::getValue('id_product');
    $video = Video::findByProductId($id_product);

    // Get default language
    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

    // Init Fields form array
    $fields_form[0]['form'] = array(
        'legend' => array(
            'title' => $this->l('Youtube video'),
        ),
        'input' => array(
            array(
                'type' => 'text',
                'label' => $this->l('Video URL'),
                'desc' => "The URL to your Youtube video",
                'name' => 'YOUTUBE_URL',
                'hint' => 'https://www.youtube.com/watch?v=XXXXXXXXX',
                'required' => false
            )
        ),
        'submit' => array(
            'title' => $this->l('Save'),
            'class' => 'btn btn-default pull-right'
        ),
        'submitAddproductAndStay' => array(
            'title' => $this->l('Save and stay'),
            'class' => 'btn btn-default pull-right'
        )
    );

    $helper = new HelperForm();

    // Module, token and currentIndex
    $helper->module = $this;
    $helper->name_controller = $this->name;
    $helper->token = Tools::getAdminTokenLite('AdminProducts');
    $helper->currentIndex = AdminController::$currentIndex.'&id_product='.$id_product.'&updateproduct';

    // Language
    $helper->default_form_language = $default_lang;
    $helper->allow_employee_form_lang = $default_lang;

    // Title and toolbar
    $helper->title = $this->displayName;
    $helper->show_toolbar = true;        // false -> remove toolbar
    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
    $helper->submit_action = 'submitAddproduct';
    $helper->toolbar_btn = array(
        'save' =>
        array(
            'desc' => $this->l('Save'),
            'href' => AdminController::$currentIndex.'&id_product='.$id_product.'&updateproduct'.
            '&token='.Tools::getAdminTokenLite('AdminProducts'),
        ),
        'back' => array(
            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminProducts'),
            'desc' => $this->l('Back to list')
        )
    );

    // Load current value
    $helper->fields_value['YOUTUBE_URL'] = $video ? 'https://www.youtube.com/watch?v=' . $video->key : '';

    $form = $helper->generateForm($fields_form);
    $regex = '/<form.[^>]*>/';
    $formContent = preg_replace($regex, '', $form);
    $formContent = preg_replace('</form>', '', $formContent);

    if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product'))))
    {
      return $formContent;
    }

  }

  public function hookActionProductUpdate($params)
  {
    $id_product = Tools::getValue("id_product");
    $currVideo = Video::findByProductId($id_product);
    $url = ($a = Tools::getValue("YOUTUBE_URL") ? trim(Tools::getValue("YOUTUBE_URL")) : null) ? $a : null;

    if (!$url && $currVideo) {
      $currVideo->delete();
      return true;
    }

    $ytRegex = '/(?:youtube\.com\/\S*(?:(?:\/e(?:mbed))?\/|watch\?(?:\S*?&?v\=))|youtu\.be\/)([a-zA-Z0-9_-]{6,11})/';
    $matches = [];
    if (preg_match($ytRegex, $url, $matches)) {
      if ($currVideo) {
        $currVideo->key = $matches[1];
        $currVideo->update();
      } else {
        $video = new Video();
        $video->id_product = $id_product;
        $video->key = $matches[1];
        $video->add();
      }
    }
    return true;
  }
}