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


use HeimrichHannot\Request\Request;

class Disclaimer
{
    const DISCLAIMER_SESSION_TARGET   = 'disclaimer_session_target';
    const DISCLAIMER_SESSION_ID       = 'disclaimer_session_id';
    const DISCLAIMER_SESSION_ACCEPTED = 'disclaimer_session_accepted';
    const DISCLAIMER_SESSION_BACK     = 'disclaimer_session_back';
    const DISCLAIMER_SESSION_REDIRECT = 'disclaimer_session_redirect';

    /**
     * Parse the disclaimer
     *
     * @param DisclaimerModel $objDisclaimer
     * @param array           $arrConfig Module / Form configuration data
     *
     * @return string The parsed disclaimer template
     */
    public static function parse(DisclaimerModel $objDisclaimer, array $arrConfig = array())
    {
        global $objPage;

        $objTemplate = new \FrontendTemplate($arrConfig['disclaimer_template']);

        // CSS Class
        $objTemplate->class = $objTemplate->getName();

        // headline
        $arrHeadline           = deserialize($objDisclaimer->headline);
        $objTemplate->headline = is_array($arrHeadline) ? $arrHeadline['value'] : $arrHeadline;
        $objTemplate->hl       = is_array($arrHeadline) ? $arrHeadline['unit'] : 'h1';

        // text
        $objTemplate->hasText = false;

        // Clean the RTE output
        if ($objDisclaimer->text != '')
        {
            $objTemplate->hasText = true;

            if ($objPage->outputFormat == 'xhtml')
            {
                $objTemplate->text = \StringUtil::toXhtml($objDisclaimer->text);
            }
            else
            {
                $objTemplate->text = \StringUtil::toHtml5($objDisclaimer->text);
            }

            $objTemplate->text = \StringUtil::encodeEmail($objTemplate->text);
        }

        $objForm = new DisclaimerForm($arrConfig);
        $objForm->setDisclaimer($objDisclaimer);
        $objTemplate->form = $objForm->generate();


        return $objTemplate->parse();
    }

    /**
     * Add an item to the list of accepted disclaimer entities
     *
     * @param $varAccept The unique item identifier
     */
    public static function addAccepted($varAccept)
    {
        $arrAccepted = \Session::getInstance()->get(static::DISCLAIMER_SESSION_ACCEPTED);

        if (!is_array($arrAccepted))
        {
            $arrAccepted = array();
        }

        if (!in_array($varAccept, $arrAccepted))
        {
            $arrAccepted[] = $varAccept;
        }

        \Session::getInstance()->set(static::DISCLAIMER_SESSION_ACCEPTED, $arrAccepted);
    }

    /**
     * Check if the unique item id is already accepted
     *
     * @param $varAccept The unique item id
     *
     * @return bool
     */
    public static function isAccepted($varAccept)
    {
        $arrAccepted = \Session::getInstance()->get(static::DISCLAIMER_SESSION_ACCEPTED);

        if (!is_array($arrAccepted))
        {
            return false;
        }

        if (!in_array($varAccept, $arrAccepted))
        {
            return false;
        }

        return true;
    }

    /**
     * Clear all accepted items, for developer purpose
     */
    public static function clearAccepted()
    {
        \Session::getInstance()->remove(static::DISCLAIMER_SESSION_ACCEPTED);
    }

    /**
     * Get the disclaimer target
     *
     * @param bool $blnReturnTarget
     *
     * @return string The target if true, otherwise the unique identifier for the item
     */
    public static function getTarget($blnReturnTarget = true)
    {
        $arrTarget = \Session::getInstance()->get(static::DISCLAIMER_SESSION_TARGET);

        if (!is_array($arrTarget))
        {
            return null;
        }

        return $blnReturnTarget ? current($arrTarget) : key($arrTarget);
    }

    /**
     * Set the disclaimer target
     *
     * @param $varAccept The unique item identifier (file path for example) for later storage and identification
     * @param $strTarget The item url that was requested and should be triggered if disclaimer will be accepted
     */
    public static function setTarget($varAccept, $strTarget)
    {
        $arrTarget = \Session::getInstance()->get(static::DISCLAIMER_SESSION_TARGET);

        if (isset($arrTarget[$varAccept]))
        {
            return;
        }

        \Session::getInstance()->set(static::DISCLAIMER_SESSION_TARGET, array($varAccept => $strTarget));
    }

    /**
     * Remove all targets from session
     */
    public static function removeTarget()
    {
        \Session::getInstance()->remove(static::DISCLAIMER_SESSION_TARGET);
    }

    /**
     * Get the back link
     *
     * @return string The back link
     */
    public static function getBack()
    {
        return \Session::getInstance()->get(static::DISCLAIMER_SESSION_BACK);
    }

    /**
     * Set back link
     *
     * @param $strBack
     */
    public static function setBack($strBack)
    {
        \Session::getInstance()->set(static::DISCLAIMER_SESSION_BACK, $strBack);
    }

    /**
     * Get Disclaimer Archive
     *
     * @return intId The disclaimer archive id
     */
    public static function getDisclaimer()
    {
        return \Session::getInstance()->get(static::DISCLAIMER_SESSION_ID);
    }

    /**
     * Set disclaimer archive id
     *
     * @param $intId
     */
    public static function setDisclaimer($intId)
    {
        \Session::getInstance()->set(static::DISCLAIMER_SESSION_ID, $intId);
    }

    /**
     * Attempt to show the disclaimer if all preconditions meet
     *
     * @param DisclaimerArchiveModel $objDisclaimerArchive
     * @param                        $varAccept
     *
     * @return bool|void Return false, if something went wrong, otherwise null
     */
    public static function show(DisclaimerArchiveModel $objDisclaimerArchive, $varAccept)
    {
        //Disclaimer::clearAccepted(); // uncomment if you want to test/debug

        // get active disclaimer by current user language and given archive id
        if (($objDisclaimer = DisclaimerModel::findPublishedByLanguageAndParent($GLOBALS['TL_LANGUAGE'], $objDisclaimerArchive->id)) === null)
        {
            return;
        }

        $strUrl = Request::getInstance()->getRequestUri();

        // return to the called action
        if (static::isAccepted($varAccept))
        {
            static::setDisclaimer(null);
            static::removeTarget();

            return;
        }

        global $objPage;

        static::setBack(\Controller::generateFrontendUrl($objPage->row(), null, null, true));
        static::setTarget($varAccept, $strUrl);
        static::setDisclaimer($objDisclaimer->pid);

        switch ($objDisclaimer->source)
        {
            case 'page':
                if (($objTarget = \PageModel::findPublishedById($objDisclaimer->jumpTo)) === null)
                {
                    break;
                }

                $strUrl = \Controller::generateFrontendUrl($objTarget->row(), null, null, true);

                \Controller::redirect($strUrl);

                break;
            case 'article':
                if (($objArticle = \ArticleModel::findByPk($objDisclaimer->articleId, array('eager' => true))) !== null
                    && ($objPid = $objArticle->getRelated('pid')) !== null
                )
                {
                    /** @var \PageModel $objPid */
                    $strUrl = ampersand(
                        $objPid->getFrontendUrl(
                            '/articles/' . ((!\Config::get('disableAlias') && $objArticle->alias != '') ? $objArticle->alias : $objArticle->id)
                        )
                    );

                    \Controller::redirect($strUrl);
                }
                break;
        }

        // HOOK: extend options by callback functions
        if (isset($GLOBALS['TL_HOOKS']['showDisclaimer']) && is_array($GLOBALS['TL_HOOKS']['showDisclaimer']))
        {
            foreach ($GLOBALS['TL_HOOKS']['showDisclaimer'] as $callback)
            {
                if (($blnReturn = \System::importStatic($callback[0])->{$callback[1]}($objDisclaimer, false)) === false)
                {
                    return false;
                }
            }
        }
    }

    /**
     * Return all disclaimer grouped by archive
     *
     * @return array
     */
    public function getDisclaimerOptions()
    {
        $arrOptions = array();

        $objDisclaimer = DisclaimerArchiveModel::findAll();

        if ($objDisclaimer === null)
        {
            return $arrOptions;
        }

        return $objDisclaimer->fetchEach('title');
    }
}