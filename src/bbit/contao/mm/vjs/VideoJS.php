<?php

namespace bbit\contao\mm\vjs;

class VideoJS extends \AbstractMultimediaPlayer {

	protected static $supported = array(
		'MultimediaYoutube',
		'MultimediaVideo',
// 		'MultimediaAudio',
	);

	public static function create(array $data = null) {
		return new self;
	}

	public static function canPlay(\Multimedia $mm) {
		foreach(self::$supported as $class) if(is_a($mm, $class)) {
			return true;
		}
		return false;
	}

	protected static $uid = 0;

	public function __construct() {
		parent::__construct();
	}

	public function embed(\Multimedia $mm) {
		try {
			if(!self::canPlay($mm)) {
				throw new \Exception(sprintf('Multimedia type [%s] not supported', get_class($mm)));
			}

			$size = $this->getSizeFor($mm);
			$data = array();
			$data['mm'] = $mm;
			$data['css'][] = '//vjs.zencdn.net/4.3/video-js.css';
			$data['js'][] = '//vjs.zencdn.net/4.3/video.js';
			$data['id'] = 'videojs' . self::$uid++;
			$data['width'] = $size[0];
			$data['height'] = $size[1];
			$data['poster'] = $mm->getPreviewImage();

			$this->compileSetup($mm, $data);
			$this->compileSources($mm, $data);

			$tpl = new \FrontendTemplate('bbit_mm_vjs');
			$tpl->setData($data);
			return $tpl->parse();

		} catch(\Exception $e) {
			if($GLOBALS['TL_CONFIG']['debug']) {
				throw $e;
			}
			$this->log($e->getMessage(), __CLASS__ . '::' . __METHOD__ . '()', TL_ERROR);
			return '';
		}
	}

	protected function compileSetup(\Multimedia $mm, array &$data) {
		if($mm instanceof \MultimediaYoutube) {
			$data['js'][] = 'system/modules/backboneit_multimedia_videojs/html/js/vjs.youtube.js';
			$data['setup']['techOrder'][] = 'youtube';
			$data['setup']['src'] = $mm->getSource();
		}
	}

	protected function compileSources(\Multimedia $mm, array &$data) {
		if($mm instanceof \MultimediaVideo) {
			foreach($mm->getSourceByType('http') as $source) if($source->isValid()) {
				$data['sources'][] = array(
					'src'	=> $source->getURL(),
					'type'	=> $source->getMIME(),
				);
			}
		}
	}

}
