<?php

/**
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class CdnController extends CMSMain {
	public static $menu_title = 'CDN';
	public static $url_segment = 'cdn';
	
	public function init() {
		parent::init();
		
		Requirements::css('content-services/css/cdn.css');
	}
	
	public function ProcessForm() {
		$config = SiteConfig::current_site_config();
		$themes = $config->getAvailableThemes();
		$themes = array_merge(array('' => ''), $themes);
		
		$fields = new FieldList(
			new DropdownField('Theme', _t('CDNController.THEME', 'Theme'), $themes, $this->request->requestVar('Theme')),
			new TextField('Directory', 'Directory to process')
		);
		
		if ($this->request->requestVar('Files') || $this->request->requestVar('Theme')) {
			$base = Director::baseFolder() . '/' . THEMES_DIR . '/' . $this->request->requestVar('Theme');

			if (is_dir($base)) {
				$files = glob($base . '/*/*');
				$fileList = array();
				foreach ($files as $file) {
					$fileList[$file] = $file;
				}
				
				$fields->push(new CheckboxField('Force', 'Force CDN update'));
				$fields->push(new CheckboxSetField('Files', 'Theme files', $fileList));
			}
		}
		
		$actions = new FieldList(new FormAction('process', 'Process'));
		
		$form = new Form($this, 'ProcessForm', $fields, $actions);
		return $form;
	}
	
	public function process($data, Form $form) {
		// glob all the top level files in themedir/css, themedir/images, and themedir/javascript
		if (isset($data['Files'])) {
			foreach ($data['Files'] as $file) {
				$force = isset($data['Forced']) && $data['Forced'];
				singleton('ContentDeliveryService')->storeThemeFile($file, $force, strpos($file, '.css') > 0);
			}
		}

		$extra = '?';
		if (isset($data['Theme'])) {
			$extra .= 'Theme=' . $data['Theme'];
		}
		
		$this->redirect('admin/cdn' . $extra);
	}
}
