<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Disclaimer;

use HeimrichHannot\Ajax\Ajax;
use HeimrichHannot\Ajax\Response\ResponseRedirect;
use HeimrichHannot\FormHybrid\Form;

class DisclaimerForm extends Form
{
    protected $strTable = 'tl_disclaimer_form';

    protected $strMethod = 'POST';

    protected $objDisclaimer;

    public function __construct($varConfig = null, $intId = 0)
    {
        $varConfig = array_merge(
            $varConfig,
            array(
                'formHybridTemplate' => 'formhybrid_disclaimer',
                'formHybridAsync'    => true,
            )
        );

        $this->objDisclaimer = DisclaimerModel::findPublishedByLanguageAndParent($GLOBALS['TL_LANGUAGE'], Disclaimer::getDisclaimer());

        parent::__construct($varConfig, $intId);
    }

    protected function compile()
    {
        if ($this->objDisclaimer->addBack)
        {
            $this->Template->addBack   = true;
            $this->Template->backLabel = $this->objDisclaimer->backLabel;
            $this->Template->backHref  = Disclaimer::getBack();
            $this->Template->backTitle = $this->objDisclaimer->backLabel;
            $this->Template->backClass = $this->objDisclaimer->backClass;
        }
    }

    public function modifyDC(&$arrDca)
    {
        if ($this->objDisclaimer->acceptLabel)
        {
            $arrDca['fields']['accept']['label'][0] = $this->objDisclaimer->acceptLabel;
        }

        if ($this->objDisclaimer->submitLabel)
        {
            $arrDca['fields']['submit']['label'] = $this->objDisclaimer->submitLabel;
        }

        if ($this->objDisclaimer->submitClass)
        {
            $arrDca['fields']['submit']['eval']['class'] = $this->objDisclaimer->submitClass;
        }

        $this->addEditableField('accept', $arrDca['fields']['accept']);
        $this->addEditableField('submit', $arrDca['fields']['submit']);
    }


    protected function onSubmitCallback(\DataContainer $dc)
    {
        Disclaimer::addAccepted(Disclaimer::getTarget(false));

        if (Ajax::isRelated(Form::FORMHYBRID_NAME) === null)
        {
            \Controller::redirect(Disclaimer::getTarget());
        }

        $objResponse = new ResponseRedirect();
        $objResponse->setUrl(Disclaimer::getTarget());
        $objResponse->setCloseModal(true);
        $objResponse->output();
    }

    public function setDisclaimer(DisclaimerModel $objDisclaimer)
    {
        $this->objDisclaimer = $objDisclaimer;
    }
}
