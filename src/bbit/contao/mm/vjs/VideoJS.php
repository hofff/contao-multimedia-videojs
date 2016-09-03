<?php

namespace bbit\contao\mm\vjs;

class VideoJS extends \AbstractMultimediaPlayer {

	protected static $supported = array(
		'MultimediaYoutube',
		'MultimediaVimeo',
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
			$data['css'][] = '//vjs.zencdn.net/5.11.6/video-js.min.css';
			$data['js'][] = '//vjs.zencdn.net/5.11.6/video.min.js';
			$data['id'] = 'bbit_mm_vjs' . self::$uid++;
			$data['poster'] = $mm->getPreviewImage();
			$data['setup']['autoplay'] = self::getAutoplay($this->isAutoplay());
			$data['setup']['loop'] = $this->isLoop();
			$data['setup']['fluid'] = $this->isResponsive();
			list(
				$data['width'],
				$data['height']
			) = $this->getSizeFor($mm);

			if($mm instanceof \MultimediaYoutube) {
				$data['js'][] = 'system/modules/hofff_multimedia_videojs/assets/js/youtube.min.js';
				$data['setup']['techOrder'][] = 'youtube';
				$data['setup']['sources'][] = array(
					'type' => 'video/youtube',
					'src' => $mm->getYoutubeLink(),
				);
				unset($data['poster']);

			} elseif($mm instanceof \MultimediaVimeo) {
				$data['js'][] = 'system/modules/hofff_multimedia_videojs/assets/js/vimeo.min.js';
				$data['setup']['techOrder'][] = 'vimeo';
				$data['setup']['sources'][] = array(
					'type' => 'video/vimeo',
					'src' => $mm->getVimeoLink(),
				);

			} elseif($mm instanceof \MultimediaVideo) {
				$data['sources'] = $this->createSources($mm);
			}

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

	protected function createSources(\MultimediaVideo $mm) {
		$sources = array();

		foreach($mm->getSourceByType('http') as $source) if($source->isValid()) {
			$sources[] = array(
				'src'	=> $source->getURL(),
				'type'	=> $source->getMIME(),
			);
		}

		return $sources;
	}

}
