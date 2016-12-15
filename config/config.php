<?php

/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['content']['disclaimer'] = array(
    'tables' => array('tl_disclaimer_archive', 'tl_disclaimer'),
    'icon'   => 'system/modules/disclaimer/assets/img/icon_disclaimer.png',
);


/**
 * Back end modules
 */
$GLOBALS['FE_MOD']['miscellaneous']['disclaimer'] = 'HeimrichHannot\Disclaimer\ModuleDisclaimer';


/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_disclaimer_archive'] = 'HeimrichHannot\Disclaimer\DisclaimerArchiveModel';
$GLOBALS['TL_MODELS']['tl_disclaimer']         = 'HeimrichHannot\Disclaimer\DisclaimerModel';

/**
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'disclaimers';
$GLOBALS['TL_PERMISSIONS'][] = 'disclaimerp';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['postDownload'][] = array('HeimrichHannot\Disclaimer\Hooks', 'postDownloadHook');
$GLOBALS['TL_HOOKS']['generatePage'][] = array('HeimrichHannot\Disclaimer\Hooks', 'generatePageHook');