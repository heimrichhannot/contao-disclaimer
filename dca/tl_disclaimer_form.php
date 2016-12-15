<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$GLOBALS['TL_DCA']['tl_disclaimer_form'] = array(
    'fields' => array(
        'accept' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_disclaimer_form']['accept'],
            'inputType' => 'checkbox',
            'eval'      => array('mandatory' => true),
        ),
        'submit' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_disclaimer_form']['submit'],
            'inputType' => 'submit',
        ),
    ),
);