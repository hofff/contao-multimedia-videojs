<?php

if(!isset($GLOBALS['TL_DCA']['tl_news4ward_article'])) {
	return;
}

$this->loadLanguageFile('tl_content');

call_user_func(function() {
	$dca = &$GLOBALS['TL_DCA']['tl_news4ward_article'];

	$dca['palettes']['bbit_mm_vjs'] = str_replace(
		',bbit_mm_player',
		',bbit_mm_player,bbit_mm_vjs_responsive,bbit_mm_vjs_autoplay,bbit_mm_vjs_loop',
		$dca['palettes']['default']
	);

	unset($dca);
});

$GLOBALS['TL_DCA']['tl_news4ward_article']['fields']['bbit_mm_vjs_responsive'] = array(
	'label'		=> &$GLOBALS['TL_LANG']['tl_content']['bbit_mm_vjs_responsive'],
	'exclude'	=> true,
	'inputType'	=> 'checkbox',
	'eval'		=> array(
		'tl_class'	=> 'w50 cbx m12',
	),
	'sql'		=> 'char(1) NOT NULL default \'\'',
);

$GLOBALS['TL_DCA']['tl_news4ward_article']['fields']['bbit_mm_vjs_autoplay'] = array(
	'label'		=> &$GLOBALS['TL_LANG']['tl_content']['bbit_mm_vjs_autoplay'],
	'exclude'	=> true,
	'inputType'	=> 'checkbox',
	'eval'		=> array(
		'tl_class'	=> 'w50 cbx m12',
	),
	'sql'		=> 'char(1) NOT NULL default \'\'',
);

$GLOBALS['TL_DCA']['tl_news4ward_article']['fields']['bbit_mm_vjs_loop'] = array(
	'label'		=> &$GLOBALS['TL_LANG']['tl_content']['bbit_mm_vjs_loop'],
	'exclude'	=> true,
	'inputType'	=> 'checkbox',
	'eval'		=> array(
		'tl_class'	=> 'w50 cbx m12',
	),
	'sql'		=> 'char(1) NOT NULL default \'\'',
);
