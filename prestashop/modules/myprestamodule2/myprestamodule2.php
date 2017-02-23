<?php
if (!defined('_PS_VERSION_')) {
  exit;
}

require_once __DIR__ . '/lib/twitteroauth/autoload.php';

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

  public function hookActionProductAdd($params)
  {
    $credentials = Configuration::getMultiple([
      'MYPRESTAMODULE2_CONSUMER_KEY',
      'MYPRESTAMODULE2_CONSUMER_SECRET',
      'MYPRESTAMODULE2_ACCESS_TOKEN',
      'MYPRESTAMODULE2_ACCESS_TOKEN_SECRET'
    ]);

    if ($credentials['MYPRESTAMODULE2_CONSUMER_KEY'] &&
      $credentials['MYPRESTAMODULE2_CONSUMER_SECRET'] &&
      $credentials['MYPRESTAMODULE2_ACCESS_TOKEN'] &&
      $credentials['MYPRESTAMODULE2_ACCESS_TOKEN_SECRET']) {
        $twitter = new Abraham\TwitterOAuth\TwitterOAuth($credentials['MYPRESTAMODULE2_CONSUMER_KEY'], $credentials['MYPRESTAMODULE2_CONSUMER_SECRET'], $credentials['MYPRESTAMODULE2_ACCESS_TOKEN'], $credentials['MYPRESTAMODULE2_ACCESS_TOKEN_SECRET']);

        $customTweet = Configuration::get('MYPRESTAMODULE2_CUSTOM_TWEET');

        $id_product = $params["id_product"];
        $product = new Product($id_product);
        $link = new Link();
        $url = $link->getProductLink($product);

        $curl = curl_init();
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,7);
        $tinyUrl = curl_exec($curl);

        $withoutName = '" ' . $customTweet . ' ' . $tinyUrl;
        $maxProductName = 139 - strlen($withoutName);

        if (strlen($product->name[1]) > $maxProductName) {
          $pName =  substr ($product->name[1], 0, $maxProductName - 3);
          $pName .= "...";
        } else {
          $pName = $product->name[1];
        }

        $msg = '"' . $pName . $withoutName;

        $twitter->post('statuses/update', [
          'status' => $msg
        ]);
      }
      return true;
  }
}