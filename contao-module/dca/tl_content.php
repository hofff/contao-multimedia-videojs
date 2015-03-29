<?php

foreach(array('bbit_mm', 'bbit_mm_mediabox') as $key) {
	$GLOBALS['TL_DCA']['tl_content']['palettes'][$key . 'bbit_mm_vjs'] = str_replace(
		',bbit_mm_player',
		',bbit_mm_player,bbit_mm_vjs_responsive,bbit_mm_vjs_autoplay,bbit_mm_vjs_loop',
		$GLOBALS['TL_DCA']['tl_content']['palettes'][$key]
	);
}

$GLOBALS['TL_DCA']['tl_content']['fields']['bbit_mm_vjs_responsive'] = array(
	'label'		=> &$GLOBALS['TL_LANG']['tl_content']['bbit_mm_vjs_responsive'],
	'exclude'	=> true,
	'inputType'	=> 'checkbox',
	'eval'		=> array(
		'tl_class'	=> 'w50 cbx m12',
	),
);

$GLOBALS['TL_DCA']['tl_content']['fields']['bbit_mm_vjs_autoplay'] = array(
	'label'		=> &$GLOBALS['TL_LANG']['tl_content']['bbit_mm_vjs_autoplay'],
	'exclude'	=> true,
	'inputType'	=> 'checkbox',
	'eval'		=> array(
		'tl_class'	=> 'w50 cbx',
	),
);

$GLOBALS['TL_DCA']['tl_content']['fields']['bbit_mm_vjs_loop'] = array(
	'label'		=> &$GLOBALS['TL_LANG']['tl_content']['bbit_mm_vjs_loop'],
	'exclude'	=> true,
	'inputType'	=> 'checkbox',
	'eval'		=> array(
		'tl_class'	=> 'w50 cbx',
	),
);
