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

  public function getContent()
  {
    $output = null;

    if (Tools::isSubmit('submit'.$this->name))
    {
        $customTweet = substr(strval(Tools::getValue('CUSTOM_TWEET')), 0, 107);
        $consumerKey = strval(Tools::getValue('CONSUMER_KEY'));
        $consumerSecret = strval(Tools::getValue('CONSUMER_SECRET'));
        $accessToken = strval(Tools::getValue('ACCESS_TOKEN'));
        $accessTokenSecret = strval(Tools::getValue('ACCESS_TOKEN_SECRET'));

        Configuration::updateValue('MYPRESTAMODULE2_CUSTOM_TWEET', $customTweet);
        Configuration::updateValue('MYPRESTAMODULE2_CONSUMER_KEY', $consumerKey);
        Configuration::updateValue('MYPRESTAMODULE2_CONSUMER_SECRET', $consumerSecret);
        Configuration::updateValue('MYPRESTAMODULE2_ACCESS_TOKEN', $accessToken);
        Configuration::updateValue('MYPRESTAMODULE2_ACCESS_TOKEN_SECRET', $accessTokenSecret);

        $output .= $this->displayConfirmation($this->l('Settings updated'));
    }
    return $output.$this->displayConfForm();
  }

  public function displayConfForm()
  {
    $credentials = Configuration::getMultiple([
      'MYPRESTAMODULE2_CONSUMER_KEY',
      'MYPRESTAMODULE2_CONSUMER_SECRET',
      'MYPRESTAMODULE2_ACCESS_TOKEN',
      'MYPRESTAMODULE2_ACCESS_TOKEN_SECRET'
    ]);
    $customTweet = Configuration::get('MYPRESTAMODULE2_CUSTOM_TWEET');

    // Get default language
    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

    // Init Fields form array
    $fields_form[0]['form'] = array(
        'legend' => array(
            'title' => $this->l('Auto-tweet on new product Conf.'),
        ),
        'input' => array(
            array(
                'type' => 'text',
                'label' => $this->l('Custom tweet message'),
                'desc' => "The custom message displayed in your tweets (max. length 107)",
                'name' => 'CUSTOM_TWEET',
                'size' => 107,
                'hint' => 'Will be like : [product_name] [custom_tweet_message] [product_short_url]',
                'required' => false
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Consumer Key'),
                'desc' => "The consumer_key for your Twitter account",
                'name' => 'CONSUMER_KEY',
                'required' => false
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Consumer Secret'),
                'desc' => "The consumer_secret for your Twitter account",
                'name' => 'CONSUMER_SECRET',
                'required' => false
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Access Token'),
                'desc' => "The access_token for you Twitter account",
                'name' => 'ACCESS_TOKEN',
                'required' => false
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Access Token Secret'),
                'desc' => "The access_token_secret for you Twitter account",
                'name' => 'ACCESS_TOKEN_SECRET',
                'required' => false
            )
        ),
        'submit' => array(
            'title' => $this->l('Save'),
            'class' => 'btn btn-default pull-right'
        )
    );

    $helper = new HelperForm();

    // Module, token and currentIndex
    $helper->module = $this;
    $helper->name_controller = $this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

    // Language
    $helper->default_form_language = $default_lang;
    $helper->allow_employee_form_lang = $default_lang;

    // Title and toolbar
    $helper->title = $this->displayName;
    $helper->show_toolbar = true;        // false -> remove toolbar
    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
    $helper->submit_action = 'submit'.$this->name;
    $helper->toolbar_btn = array(
        'save' =>
        array(
            'desc' => $this->l('Save'),
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
            '&token='.Tools::getAdminTokenLite('AdminModules'),
        ),
        'back' => array(
            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Back to list')
        )
    );

    // Load current value
    $helper->fields_value['CONSUMER_KEY'] =  $credentials['MYPRESTAMODULE2_CONSUMER_KEY'] ? $credentials['MYPRESTAMODULE2_CONSUMER_KEY'] : '';
    $helper->fields_value['CONSUMER_SECRET'] =  $credentials['MYPRESTAMODULE2_CONSUMER_SECRET'] ? $credentials['MYPRESTAMODULE2_CONSUMER_SECRET'] : '';
    $helper->fields_value['ACCESS_TOKEN'] =  $credentials['MYPRESTAMODULE2_ACCESS_TOKEN'] ? $credentials['MYPRESTAMODULE2_ACCESS_TOKEN'] : '';
    $helper->fields_value['ACCESS_TOKEN_SECRET'] =  $credentials['MYPRESTAMODULE2_ACCESS_TOKEN_SECRET'] ? $credentials['MYPRESTAMODULE2_ACCESS_TOKEN_SECRET'] : '';
    $helper->fields_value['CUSTOM_TWEET'] =  $customTweet ? $customTweet : '';

    $form = $helper->generateForm($fields_form);
    return $form;
  }
}