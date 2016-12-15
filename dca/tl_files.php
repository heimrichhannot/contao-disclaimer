<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$arrDca = &$GLOBALS['TL_DCA']['tl_files'];

/**
 * Palettes
 */
$arrDca['palettes']['default'] = str_replace('meta', 'meta;{disclaimer_legend},disclaimer', $arrDca['palettes']['default']);


/**
 * Fields
 */
$arrFields = array(
    'disclaimer' => array(
        'label'            => &$GLOBALS['TL_LANG']['tl_files']['disclaimer'],
        'exclude'          => true,
        'inputType'        => 'select',
        'foreignKey'       => 'tl_disclaimer.title',
        'options_callback' => array('HeimrichHannot\Disclaimer\Disclaimer', 'getDisclaimerOptions'),
        'eval'             => array('tl_class' => 'w50 clr', 'includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'long'),
        'sql'              => "int(10) unsigned NOT NULL default '0'",
        'relation'         => array('type' => 'belongsTo', 'load' => 'lazy'),
    ),
);

$arrDca['fields'] = array_merge($arrDca['fields'], $arrFields);