<?php

class VimeoTool_Module extends Core_ModuleBase  {
	/**
	 * Creates the module information object
	 * @return Core_ModuleInfo
	 */
	 
	protected function createModuleInfo() {
	  return new Core_ModuleInfo(
			"Vimeo Tool",
			"Various tools to use the Vimeo API in Lemonstand",
			"Keyed-Up Media LLC" );
	}
	
	public function listSettingsItems()	{
		return array(
			array(
				'title'=>'Vimeo Tool',
				'url'=>'/vimeotool/settings',
				'icon'=>'/modules/vimeotool/resources/img/vimeo.png',
				'description'=>'Vimeo Authentication Settings',
				'sort_id'=>40,
				'section'=>'CMS'
			)
		);
	}

}
