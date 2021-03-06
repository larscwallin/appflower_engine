<?php
/**
 * extJs desktop layout
 */
class afExtjsDesktopLayout extends afExtjsLayout
{	
	public function start($attributes=array())
	{
		$this->afExtjs->setAddons(array ('css' => array($this->afExtjs->getPluginsDir().'desktop/css/desktop.css'), 'js' => array($this->afExtjs->getPluginsDir().'desktop/js/StartMenu.js',$this->afExtjs->getPluginsDir().'desktop/js/TaskBar.js',$this->afExtjs->getPluginsDir().'desktop/js/Desktop.js')));
		
		if (is_readable(sfConfig::get('sf_app_lib_dir').'/helper/afExtjsDesktopStartMenuHelper.php'))
        {
		  sfProjectConfiguration::getActive()->loadHelpers(array('afExtjsDesktopStartMenu'));
        }
        else {
            sfProjectConfiguration::getActive()->loadHelpers(array('afExtjsDesktopStartMenuDefault'));
        }
		
		$this->addInitMethodSource("this.startConfig = startMenuConfig;this.desktop = new Ext.Desktop(this);");
	}
	
	public function getShortcuts()
	{
	    if (is_readable(sfConfig::get('sf_app_lib_dir').'/helper/afExtjsDesktopLinksHelper.php'))
        {
          sfProjectConfiguration::getActive()->loadHelpers(array('afExtjsDesktopLinks'));
        }
        else {
            sfProjectConfiguration::getActive()->loadHelpers(array('afExtjsDesktopLinksDefault'));
        }
	}
	
	public function getBackgroundColor()
	{
	    return sfConfig::get('app_appFlower_desktopBackgroundColor');
	}
	
	public function getBackgroundImage()
	{
	    return sfConfig::get('app_appFlower_desktopBackgroundImage');
	}
	
	public function end()
	{		
		$this->addInitMethodSource("
		setTimeout(function(){			
			Ext.get('loading').remove();
	        Ext.get('loading-mask').fadeOut({remove:true});
	        
	        afApp.loadFirst(true);
	    }, 250);
		");		
		
		parent::end();
	}
}
?>
