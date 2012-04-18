<?php

/**
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class CdnController extends LeftAndMain {
	public static $menu_title = 'CDN';
	public static $url_segment = 'cdn';
	
	public function init() {
		parent::init();
		
		Requirements::javascript('content-services/javascript/cdn.js');
		Requirements::css('content-services/css/cdn.css');
	}
	

	public function EditForm($theme = null) {
		$config = SiteConfig::current_site_config();
		$themes = $config->getAvailableThemes();
		$themes = array_merge(array('' => ''), $themes);
		
		$tabs = new TabSet('Root');
		$tabs->push(new Tab('Main'));
		
		$theme = $theme ? $theme : $this->request->requestVar('Theme');
		
		$fields = new FieldSet($tabs);
		$fields->addFieldsToTab('Root.Main', array(
			new DropdownField('Theme', _t('CDNController.THEME', 'Theme'), $themes, $theme),
			new TextField('Directory', 'Directory to process')
		));
		
		if ($this->request->requestVar('Files') || $theme) {
			$base = Director::baseFolder() . '/' . THEMES_DIR . '/' . $theme;

			if (is_dir($base)) {
				$files = glob($base . '/*/*');
				$fileList = array();
				foreach ($files as $file) {
					$fileList[$file] = $file;
				}
				
				$fields->addFieldToTab('Root.Main', new CheckboxField('Force', 'Force CDN update'));
				$fields->addFieldToTab('Root.Main', new CheckboxSetField('Files', 'Theme files', $fileList));
			}
		}
		
		$actions = new FieldSet(new FormAction('process', 'Process'));
		
		$form = new Form($this, 'EditForm', $fields, $actions);
		return $form;
	}
	
	public function process($data, Form $form) {
		// glob all the top level files in themedir/css, themedir/images, and themedir/javascript
		if (isset($data['Files'])) {
			$force = isset($data['Force']) && $data['Force'] > 0;
			foreach ($data['Files'] as $file) {
				singleton('ContentDeliveryService')->storeThemeFile($file, $force, strpos($file, '.css') > 0);
			}
		}

		$extra = '?';
		if (isset($data['Theme'])) {
			$_REQUEST['Theme'] = $data['Theme'];
			$extra .= 'Theme=' . $data['Theme'];
		}
		
		if ($this->isAjax() && isset($data['Theme']) && strlen($data['Theme'])) {
			return $this->EditForm($data['Theme'])->forTemplate();
		} else {
			$this->redirect('admin/cdn' . $extra);
		}
	}
}
