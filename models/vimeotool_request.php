<?php

	class VimeoTool_Request {
	
		private $configuration;
		private $vimeo;
		private $lastresponse;
		private $lasterror;
		
		// Default params to be populated in the construct
		private $params = array();
		
		public function __construct() {
			
			// Load the configuration class
			$this->configuration = VimeoTool_Configuration::create();
		
			// Include the vimeo library
			require_once( __DIR__ . "/../vendor/php-vimeo/vimeo.php" );

			// Create an instance of phpVimeo (Vimeo PHP Library)
			$this->vimeo = new phpVimeo(
				$this->configuration->consumer_key,
				$this->configuration->consumer_secret,
				$this->configuration->token, 
				$this->configuration->token_secret 
			);
		
		}
		
		public static function create() {
			return new self();			
		}
		
		public function set_params( $params = array() ) {
			$this->params[] = $params;
			return $this;
		}
	
		public function send_request( $method = '' ) {
			return $this->vimeo->call( $method, $this->params );
		}

	}
