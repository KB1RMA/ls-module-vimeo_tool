<?php

class VimeoTool_Settings extends Backend_SettingsController {

	protected $access_for_groups = array(Users_Groups::admin);
	public $implement = 'Db_FormBehavior';

	public $form_edit_title = 'VimeoTool Settings';
	public $form_model_class = 'VimeoTool_Configuration';
	public $form_redirect = null;

	public $redirect_uri = null;
	public $vimeo;

	public function __construct() {
	
		parent::__construct();		
		
		$this->app_tab = 'Vimeo Tool';
		$this->app_module_name = 'VimeoTool';

		$this->app_page = 'settings';
		
		$this->redirect_uri  = site_url(url('/vimeotool/settings/authenticate/'));
		$this->form_redirect = url('/vimeotool/settings/authenticate/');

	}

	public function index()	{

			$this->app_page_title = 'Vimeo Tool Configuration';
		
			$obj = new VimeoTool_Configuration();
			$obj = $obj->load();
			
			$this->viewData['form_model'] = $obj;		
			$this->viewData['authenticated'] = $this->test_authentication();		
	}
	
	protected function index_onSave() {
		
		try {
			$obj = new VimeoTool_Configuration();
			$obj = $obj->load();

			$obj->save(post($this->form_model_class, array()), $this->formGetEditSessionKey());

			echo Backend_Html::flash_message('Configuration saved.');

		} catch (Exception $ex) {
			Phpr::$response->ajaxReportException($ex, true, true);
		}
		
	}

	protected function index_onAuthenticate() {
		Phpr::$response->redirect( url('/vimeotool/settings/authenticate/') );
	}
	
	public function authenticate() {
		
		$configuration = VimeoTool_Configuration::create();

		$token = Phpr::$request->get_value_array('oauth_token');
		
		// If token isn't sent, redirect to the vimeo authentication link		
		if ( empty($token) ) {
		
			// Wipe out any stale request tokens if we're re authenticating
			$configuration->token = '';
			$configuration->token_secret = '';

			$this->_load_vimeo();

			// Create request token and save	
			$requestToken = $this->vimeo->getRequestToken( 'http://creatingsuperkids.local.keyedupmedia.com/backdoor/vimeotool/settings/authenticate/' );
			$configuration->token = $requestToken['oauth_token'];
			$configuration->token_secret = $requestToken['oauth_token_secret'];
			$configuration->save();

			// Redirect to vimeo authorization link
			Phpr::$response->redirect( $this->vimeo->getAuthorizeUrl( $configuration->token, 'write') );		
			return;
		}

		$this->_load_vimeo();
	
		// Exchange request token for access token and save
		$accessToken = $this->vimeo->getAccessToken( Phpr::$request->get_value_array('oauth_verifier') );

		$configuration->token = $accessToken['oauth_token'];
		$configuration->token_secret = $accessToken['oauth_token_secret'];
	
		$configuration->save();
		
		// Send back to the settings page
		Phpr::$response->redirect( url('/vimeotool/settings/') );

	}
	
	public function test_authentication() {
		
		$this->_load_vimeo();
		
		$params = array();

		try {
			return $this->vimeo->call('vimeo.videos.getUploaded');
		} catch (VimeoAPIException $e) {
			return false;
		}
	
	}

	private function _load_vimeo() {
		
		// Load configuration class	
		$configuration = VimeoTool_Configuration::create();
		
		require_once( __DIR__ . "/../vendor/php-vimeo/vimeo.php" );

		try {
			$this->vimeo = new phpVimeo($configuration->consumer_key,$configuration->consumer_secret);
		} catch (VimeoAPIException $e) {
			$this->vimeo = false;
		}
	
		$this->vimeo->setToken( $configuration->token, $configuration->token_secret );
		
	}

}
