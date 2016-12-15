<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Oliver Janke <o.janke@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$arrDca = &$GLOBALS['TL_DCA']['tl_page'];

/**
 * Palettes
 */
$arrDca['palettes']['root']           =
    str_replace('{protected_legend:hide}', '{disclaimer_legend:hide},showDisclaimer;{protected_legend:hide}', $arrDca['palettes']['root']);
$arrDca['palettes']['__selector__'][] = 'showDisclaimer';

$arrDca['subpalettes']['showDisclaimer'] = 'disclaimer';


/**
 * Fields
 */
$arrFields = array(
    'showDisclaimer' => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_page']['showDisclaimer'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array('submitOnChange' => true),
        'sql'       => "char(1) NOT NULL default '0'",
    ),
    'disclaimer'     => array(
        'label'            => &$GLOBALS['TL_LANG']['tl_page']['disclaimer'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => array('HeimrichHannot\Disclaimer\Disclaimer', 'getDisclaimerOptions'),
        'eval'             => array(
            'chosen'             => true,
            'includeBlankOption' => true,
            'mandatory'          => true,
            'tl_class'           => 'long',
        ),
        'sql'              => "int(10) unsigned NOT NULL default '0'",
    ),
);

$arrDca['fields'] = array_merge($arrDca['fields'], $arrFields);