<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(
    array(
        'HeimrichHannot',
    )
);


/**
 * Register the classes
 */
ClassLoader::addClasses(
    array(
        // Models
        'HeimrichHannot\Disclaimer\DisclaimerArchiveModel'    => 'system/modules/disclaimer/models/DisclaimerArchiveModel.php',
        'HeimrichHannot\Disclaimer\DisclaimerModel'           => 'system/modules/disclaimer/models/DisclaimerModel.php',

        // Modules
        'HeimrichHannot\Disclaimer\ModuleDisclaimer'          => 'system/modules/disclaimer/modules/ModuleDisclaimer.php',

        // Classes
        'HeimrichHannot\Disclaimer\DisclaimerForm'            => 'system/modules/disclaimer/classes/DisclaimerForm.php',
        'HeimrichHannot\Disclaimer\Hooks'                     => 'system/modules/disclaimer/classes/Hooks.php',
        'HeimrichHannot\Disclaimer\Disclaimer'                => 'system/modules/disclaimer/classes/Disclaimer.php',
        'HeimrichHannot\Disclaimer\Backend\ModuleBackend'     => 'system/modules/disclaimer/classes/backend/ModuleBackend.php',
        'HeimrichHannot\Disclaimer\Backend\DisclaimerBackend' => 'system/modules/disclaimer/classes/backend/DisclaimerBackend.php',
    )
);


/**
 * Register the templates
 */
TemplateLoader::addFiles(
    array(
        'formhybrid_disclaimer' => 'system/modules/disclaimer/templates/forms',
        'mod_disclaimer'        => 'system/modules/disclaimer/templates/modules',
        'disclaimer_default'    => 'system/modules/disclaimer/templates/disclaimers',
    )
);
