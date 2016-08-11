<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$arrDca = &$GLOBALS['TL_DCA']['tl_module'];


/**
 * Palettes
 */
$arrDca['palettes']['disclaimer'] = '{title_legend},name,headline,type;{config_legend},disclaimer_archives;{template_legend:hide},disclaimer_template,customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

/**
 * Fields
 */
$arrFields = array
(
	'disclaimer_archives' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['disclaimer_archives'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'options_callback'        => array('HeimrichHannot\Disclaimer\Backend\ModuleBackend', 'getDisclaimerArchives'),
		'eval'                    => array('multiple'=>true, 'mandatory'=>true),
		'sql'                     => "blob NULL"
	),
	'disclaimer_template' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['disclaimer_template'],
		'default'                 => 'disclaimer_default',
		'exclude'                 => true,
		'inputType'               => 'select',
		'options_callback'        => array('HeimrichHannot\Disclaimer\Backend\ModuleBackend', 'getDisclaimerTemplates'),
		'eval'                    => array('tl_class'=>'w50'),
		'sql'                     => "varchar(64) NOT NULL default ''"
	)
);

$arrDca['fields'] = array_merge($arrDca['fields'], $arrFields);