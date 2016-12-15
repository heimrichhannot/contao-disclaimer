<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Disclaimer\Backend;


use HeimrichHannot\Disclaimer\DisclaimerModel;

class DisclaimerBackend extends \Backend
{
    /**
     * Make sure there is only one fallback per disclaimer
     *
     * @param mixed          $varValue
     * @param \DataContainer $dc
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function checkFallback($varValue, \DataContainer $dc)
    {
        if ($varValue == '')
        {
            return '';
        }

        $objPage = \Database::getInstance()->prepare("SELECT id FROM tl_disclaimer WHERE fallback=1 AND pid=? AND id!=?")->execute(
                $dc->activeRecord->pid,
                $dc->activeRecord->id
            );

        if ($objPage->numRows)
        {
            throw new \Exception($GLOBALS['TL_LANG']['ERR']['multipleDisclaimerFallback']);
        }

        return $varValue;
    }

    /**
     * Get all articles and return them as array
     *
     * @param  \DataContainer $dc
     *
     * @return array
     */
    public function getArticleAlias(\DataContainer $dc)
    {
        $arrPids  = array();
        $arrAlias = array();

        if (!\BackendUser::getInstance()->isAdmin)
        {
            foreach ($this->User->pagemounts as $id)
            {
                $arrPids[] = $id;
                $arrPids   = array_merge($arrPids, $this->Database->getChildRecords($id, 'tl_page'));
            }

            if (empty($arrPids))
            {
                return $arrAlias;
            }

            $objAlias = $this->Database->prepare(
                "SELECT a.id, a.title, a.inColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid WHERE a.pid IN(" . implode(
                    ',',
                    array_map(
                        'intval',
                        array_unique(
                            $arrPids
                        )
                    )
                ) . ") ORDER BY parent, a.sorting"
            )->execute($dc->id);
        }
        else
        {
            $objAlias = $this->Database->prepare(
                "SELECT a.id, a.title, a.inColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid ORDER BY parent, a.sorting"
            )->execute($dc->id);
        }

        if ($objAlias->numRows)
        {
            \System::loadLanguageFile('tl_article');

            while ($objAlias->next())
            {
                $arrAlias[$objAlias->parent][$objAlias->id] =
                    $objAlias->title . ' (' . ($GLOBALS['TL_LANG']['COLS'][$objAlias->inColumn] ?: $objAlias->inColumn) . ', ID ' . $objAlias->id . ')';
            }
        }

        return $arrAlias;
    }

    /**
     * Add the source options depending on the allowed fields (see #5498)
     *
     * @param  \DataContainer $dc
     *
     * @return array
     */
    public function getSourceOptions(\DataContainer $dc)
    {
        if (\BackendUser::getInstance()->isAdmin)
        {
            $arrOptions = array('page', 'article');

            // HOOK: extend options by callback functions
            if (isset($GLOBALS['TL_HOOKS']['getDisclaimerSourceOptions']) && is_array($GLOBALS['TL_HOOKS']['getDisclaimerSourceOptions']))
            {
                foreach ($GLOBALS['TL_HOOKS']['getDisclaimerSourceOptions'] as $callback)
                {
                    $arrOptions = \System::importStatic($callback[0])->{$callback[1]}($arrOptions, $dc);
                }
            }

            return $arrOptions;
        }

        $arrOptions = array();

        // Add the "page" option
        if (\BackendUser::getInstance()->hasAccess('tl_disclaimer::jumpTo', 'alexf'))
        {
            $arrOptions[] = 'page';
        }

        // Add the "article" option
        if (\BackendUser::getInstance()->hasAccess('tl_disclaimer::articleId', 'alexf'))
        {
            $arrOptions[] = 'article';
        }


        // HOOK: extend options by callback functions
        if (isset($GLOBALS['TL_HOOKS']['getDisclaimerSourceOptions']) && is_array($GLOBALS['TL_HOOKS']['getDisclaimerSourceOptions']))
        {
            foreach ($GLOBALS['TL_HOOKS']['getDisclaimerSourceOptions'] as $callback)
            {
                $arrOptions = \System::importStatic($callback[0])->{$callback[1]}($arrOptions, $dc);
            }
        }

        // Add the option currently set
        if ($dc->activeRecord && $dc->activeRecord->source != '')
        {
            $arrOptions[] = $dc->activeRecord->source;
            $arrOptions   = array_unique($arrOptions);
        }

        return $arrOptions;
    }

    public function getLanguageOptions(\DataContainer $dc)
    {
        $arrLanguages = \Controller::getLanguages();

        // Only show the root page languages (see #7112, #7667)
        $objRootLangs = \Database::getInstance()->query("SELECT REPLACE(language, '-', '_') AS language FROM tl_page WHERE type='root'");
        $arrOptions   = array_intersect_key($arrLanguages, array_flip($objRootLangs->fetchEach('language')));

        $objDisclaimer = DisclaimerModel::findByPk($dc->id);

        if ($objDisclaimer === null)
        {
            return $arrOptions;
        }

        $objDisclaimerSiblings = DisclaimerModel::findByPid($objDisclaimer->pid);

        if ($objDisclaimerSiblings === null)
        {
            return $arrOptions;
        }

        $arrUsedLanguages = array();


        while ($objDisclaimerSiblings->next())
        {
            if ($objDisclaimerSiblings->id == $objDisclaimer->id)
            {
                continue;
            }

            $arrUsedLanguages[$objDisclaimerSiblings->language] = $arrLanguages[$objDisclaimerSiblings->language];

        }

        if (!empty($arrUsedLanguages))
        {
            $arrOptions = array_diff_key($arrOptions, $arrUsedLanguages);
        }

        return $arrOptions;
    }

    public function listChildren($arrRow)
    {
        \Controller::loadLanguageFile('languages');

        $language = $GLOBALS['TL_LANG']['LNG'][$arrRow['language']];

        return '<div class="tl_content_left">' . ($language ? '[' . $language . '] ' : '') . ($arrRow['title'] ?: $arrRow['id'])
               . ' <span style="color:#b3b3b3; padding-left:3px">[' . \Date::parse(\Config::get('datimFormat'), trim($arrRow['dateAdded'])) . ']</span></div>';
    }

    public function checkPermission()
    {
        $objUser     = \BackendUser::getInstance();
        $objSession  = \Session::getInstance();
        $objDatabase = \Database::getInstance();

        if ($objUser->isAdmin)
        {
            return;
        }

        // Set the root IDs
        if (!is_array($objUser->s) || empty($objUser->s))
        {
            $root = array(0);
        }
        else
        {
            $root = $objUser->s;
        }

        $id = strlen(\Input::get('id')) ? \Input::get('id') : CURRENT_ID;

        // Check current action
        switch (\Input::get('act'))
        {
            case 'paste':
                // Allow
                break;

            case 'create':
                if (!strlen(\Input::get('pid')) || !in_array(\Input::get('pid'), $root))
                {
                    \Controller::log(
                        'Not enough permissions to create disclaimer items in disclaimer archive ID "' . \Input::get('pid') . '"',
                        'tl_disclaimer checkPermission',
                        TL_ERROR
                    );
                    \Controller::redirect('contao/main.php?act=error');
                }
                break;

            case 'cut':
            case 'copy':
                if (!in_array(\Input::get('pid'), $root))
                {
                    \Controller::log(
                        'Not enough permissions to ' . \Input::get('act') . ' disclaimer item ID "' . $id . '" to disclaimer archive ID "' . \Input::get('pid')
                        . '"',
                        'tl_disclaimer checkPermission',
                        TL_ERROR
                    );
                    \Controller::redirect('contao/main.php?act=error');
                }
            // NO BREAK STATEMENT HERE

            case 'edit':
            case 'show':
            case 'delete':
            case 'toggle':
            case 'feature':
                $objArchive = $objDatabase->prepare("SELECT pid FROM tl_disclaimer WHERE id=?")->limit(1)->execute($id);

                if ($objArchive->numRows < 1)
                {
                    \Controller::log('Invalid disclaimer item ID "' . $id . '"', 'tl_disclaimer checkPermission', TL_ERROR);
                    \Controller::redirect('contao/main.php?act=error');
                }

                if (!in_array($objArchive->pid, $root))
                {
                    \Controller::log(
                        'Not enough permissions to ' . \Input::get('act') . ' disclaimer item ID "' . $id . '" of disclaimer archive ID "' . $objArchive->pid
                        . '"',
                        'tl_disclaimer checkPermission',
                        TL_ERROR
                    );
                    \Controller::redirect('contao/main.php?act=error');
                }
                break;

            case 'select':
            case 'editAll':
            case 'deleteAll':
            case 'overrideAll':
            case 'cutAll':
            case 'copyAll':
                if (!in_array($id, $root))
                {
                    \Controller::log('Not enough permissions to access disclaimer archive ID "' . $id . '"', 'tl_disclaimer checkPermission', TL_ERROR);
                    \Controller::redirect('contao/main.php?act=error');
                }

                $objArchive = $objDatabase->prepare("SELECT id FROM tl_disclaimer WHERE pid=?")->execute($id);

                if ($objArchive->numRows < 1)
                {
                    \Controller::log('Invalid disclaimer archive ID "' . $id . '"', 'tl_disclaimer checkPermission', TL_ERROR);
                    \Controller::redirect('contao/main.php?act=error');
                }

                $session                   = $objSession->getData();
                $session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $objArchive->fetchEach('id'));
                $objSession->setData($session);
                break;

            default:
                if (strlen(\Input::get('act')))
                {
                    \Controller::log('Invalid command "' . \Input::get('act') . '"', 'tl_disclaimer checkPermission', TL_ERROR);
                    \Controller::redirect('contao/main.php?act=error');
                }
                elseif (!in_array($id, $root))
                {
                    \Controller::log('Not enough permissions to access disclaimer archive ID ' . $id, 'tl_disclaimer checkPermission', TL_ERROR);
                    \Controller::redirect('contao/main.php?act=error');
                }
                break;
        }
    }

    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        $objUser = \BackendUser::getInstance();

        if (strlen(\Input::get('tid')))
        {
            $this->toggleVisibility(\Input::get('tid'), (\Input::get('state') == 1));
            \Controller::redirect($this->getReferer());
        }

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$objUser->isAdmin && !$objUser->hasAccess('tl_disclaimer::published', 'alexf'))
        {
            return '';
        }

        $href .= '&amp;tid=' . $row['id'] . '&amp;state=' . ($row['published'] ? '' : 1);

        if (!$row['published'])
        {
            $icon = 'invisible.gif';
        }

        return '<a href="' . $this->addToUrl($href) . '" title="' . specialchars($title) . '"' . $attributes . '>' . \Image::getHtml($icon, $label) . '</a> ';
    }

    public function toggleVisibility($intId, $blnVisible)
    {
        $objUser     = \BackendUser::getInstance();
        $objDatabase = \Database::getInstance();

        // Check permissions to publish
        if (!$objUser->isAdmin && !$objUser->hasAccess('tl_disclaimer::published', 'alexf'))
        {
            \Controller::log('Not enough permissions to publish/unpublish item ID "' . $intId . '"', 'tl_disclaimer toggleVisibility', TL_ERROR);
            \Controller::redirect('contao/main.php?act=error');
        }

        $objVersions = new \Versions('tl_disclaimer', $intId);
        $objVersions->initialize();

        // Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA']['tl_disclaimer']['fields']['published']['save_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_disclaimer']['fields']['published']['save_callback'] as $callback)
            {
                $this->import($callback[0]);
                $blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
            }
        }

        // Update the database
        $objDatabase->prepare("UPDATE tl_disclaimer SET tstamp=" . time() . ", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")->execute($intId);

        $objVersions->create();
        \Controller::log(
            'A new version of record "tl_disclaimer.id=' . $intId . '" has been created' . $this->getParentEntries('tl_disclaimer', $intId),
            'tl_disclaimer toggleVisibility()',
            TL_GENERAL
        );
    }
}