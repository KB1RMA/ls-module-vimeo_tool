<?

class VimeoTool_Configuration extends Core_Configuration_Model	{

	public $record_code = 'vimeotool_configuration';

	public static function create() {
	
		$obj = new self();
		return $obj->load();
		
	}
	
	protected function build_form() {
	
		$this->add_field('consumer_key', 'Client ID', 'full', db_varchar)->tab('Authentication')->comment('Client ID from Vimeo', 'above');
		$this->add_field('consumer_secret', 'Client Secret', 'full', db_varchar)->tab('Authentication')->comment('Client Secret from Vimeo', 'above');
		$this->add_field('token', 'Token', 'left', db_varchar)->tab('Authentication')->comment('Token after you authenticate', 'above')->disabled();
		$this->add_field('token_secret', 'Token Secret', 'right', db_varchar)->tab('Authentication')->comment('Token Secret after you authenticate', 'above')->disabled();

		$this->add_field('callback', 'Callback', 'full', db_varchar)->tab('Options')->comment('URL Vimeo authentication returns you to. You probably won\'t have to change this.', 'above');
		$this->add_field('caching', 'Caching', 'full', db_bool)->tab('Options')->comment('Do you want to enable caching on api requests?');
	}
	
	protected function init_config_data() {
		
		$this->caching = true;
		$this->callback = Cms_Html::site_url('/backdoor/vimeotool/settings/authenticate');

	}

}
