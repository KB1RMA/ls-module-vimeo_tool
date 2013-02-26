<?php

	class VimeoTool_Request {
	
		// Expose the raw API if someone wants it
		public $vimeo;

		private $configuration;
		private $lastresponse;
		private $lasterror;
		private $cache;
		private $useCache = false;
		
		// Default params to be populated in the construct
		private $params = array();
		
		public function __construct() {
			
			// Load the configuration class
			$this->configuration = VimeoTool_Configuration::create();

			if ( $this->configuration->caching ) {
				$this->cache = Core_CacheBase::create();
				$this->useCache = true;
			}
		
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
	
		public function send_request( $method = '', $recache = false ) {
			
			if ( $this->useCache ) {
				$key = 'vimeo_tool_' . str_replace(".", "", $method);
				
				$this->cache->create_key($key, $recache);
				$response = $this->cache->get($key);

				if ( !$response || $recache ) {
					$response = $this->vimeo->call( $method, $this->params );
					$this->cache->set($key, $response);
					return $response;
				}

				return $response;
			}	

			// If we're not caching anything
			return $this->vimeo->call( $method, $this->params );
		}

	}
