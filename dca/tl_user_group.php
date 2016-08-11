<?php

/**
 * Extend default palette
 */
$GLOBALS['TL_DCA']['tl_user_group']['palettes']['default'] = str_replace('fop;', 'fop;{disclaimer_legend},disclaimers,disclaimerp;', $GLOBALS['TL_DCA']['tl_user_group']['palettes']['default']);


/**
 * Add fields to tl_user_group
 */
$GLOBALS['TL_DCA']['tl_user_group']['fields']['disclaimers'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['disclaimers'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_disclaimer_archive.title',
	'eval'                    => array('multiple' => true),
	'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_user_group']['fields']['disclaimerp'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['disclaimerp'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => array('create', 'delete'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('multiple' => true),
	'sql'                     => "blob NULL"
);