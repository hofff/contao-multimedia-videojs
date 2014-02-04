<?php

namespace bbit\contao\mm\vjs;

class VideoJS extends \AbstractMultimediaPlayer {

	protected static $supported = array(
// 		'MultimediaYoutube',
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
			$data = array(
				'mm'		=> $mm,
				'css'		=> '//vjs.zencdn.net/4.2/video-js.css',
				'js'		=> '//vjs.zencdn.net/4.2/video.js',
				'id'		=> 'videojs' . self::$uid++,
				'width'		=> $size[0],
				'height'	=> $size[1],
				'poster'	=> $mm->getPreviewImage(),
				'setup'		=> array(),
				'sources'	=> $this->compileSources($mm),
			);

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

	protected function compileSources(\MultimediaVideo $mm) {
		foreach($mm->getSourceByType('http') as $source) if($source->isValid()) {
			$sources[] = array(
				'src'	=> $source->getURL(),
				'type'	=> $source->getMIME(),
			);
		}
		return (array) $sources;
	}

}
