<?php

namespace bbit\contao\mm\vjs;

class VideoJS extends \AbstractMultimediaPlayer {

	protected static $supported = array(
		'MultimediaYoutube',
		'MultimediaVideo',
// 		'MultimediaAudio',
	);

	public static function create(array $data = null) {
		$player = new self;
		$player->setResponsive($data['bbit_mm_vjs_responsive']);
		$player->setAutoplay($data['bbit_mm_vjs_autoplay']);
		$player->setLoop($data['bbit_mm_vjs_loop']);
		return $player;
	}

	public static function canPlay(\Multimedia $mm) {
		foreach(self::$supported as $class) if(is_a($mm, $class)) {
			return true;
		}
		return false;
	}

	protected static $uid = 0;

	private $responsive = false;

	private $autoplay = false;

	private $loop = false;

	public function __construct() {
		parent::__construct();
	}

	public function isResponsive() {
		return $this->responsive;
	}

	public function setResponsive($responsive) {
		$this->responsive = (bool) $responsive;
	}

	public function isAutoplay() {
		return $this->autoplay;
	}

	public function setAutoplay($autoplay) {
		$this->autoplay = (bool) $autoplay;
	}

	public function isLoop() {
		return $this->loop;
	}

	public function setLoop($loop) {
		$this->loop = (bool) $loop;
	}

	public function embed(\Multimedia $mm) {
		try {
			if(!self::canPlay($mm)) {
				throw new \Exception(sprintf('Multimedia type [%s] not supported', get_class($mm)));
			}

			$data = array();
			$data['mm'] = $mm;
// 			$data['css'][] = '//vjs.zencdn.net/4.5/video-js.css';
// 			$data['js'][] = '//vjs.zencdn.net/4.5/video.js';
			$data['head'][] = '<link rel="stylesheet" href="//vjs.zencdn.net/4.5/video-js.css">';
			$data['head'][] = '<script type="text/javascript" src="//vjs.zencdn.net/4.5/video.js"></script>';
			$data['id'] = 'bbit_mm_vjs' . self::$uid++;
			if($this->isResponsive()) {
				$padding = round(1 / $mm->getRatio() * 100, 2);
				$css = <<<CSS
<style type="text/css"><!--
div#{$data['id']} { padding-top: $padding%; }
div#{$data['id']}.vjs-fullscreen { padding-top: 0; }
//--></style>
CSS;
				$data['head'][] = $css;
				$data['width'] = 'auto';
				$data['height'] = 'auto';
			} else {
				$size = $this->getSizeFor($mm);
				$data['width'] = $size[0];
				$data['height'] = $size[1];
			}
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
		$data['setup']['autoplay'] = self::getAutoplay($this->isAutoplay());
		$data['setup']['loop'] = $this->isLoop();

		if($mm instanceof \MultimediaYoutube) {
// 			$data['js'][] = 'system/modules/backboneit_multimedia_videojs/html/js/vjs.youtube.js';
			$data['head'][] = '<script type="text/javascript" src="system/modules/backboneit_multimedia_videojs/html/js/vjs.youtube.js"></script>';
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
