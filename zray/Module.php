<?php

namespace OnlineEditor;

class Module extends \ZRay\ZRayModule {
	
	public function config() {
	    return array(
	        'extension' => array(
				'name' => 'Online Editor',
			),
	        // configure  custom panels
            'defaultPanels' => array(
             ),
	        'panels' => array(
	            'editor' => array(
	                'display'       => true,
	                'alwaysShow'    => true,
	                'logo'          => 'logo.png',
	                'menuTitle' 	=> 'Editor',
	                'panelTitle'	=> 'Editor',
	            ),
	         )
	    );
	}	
}