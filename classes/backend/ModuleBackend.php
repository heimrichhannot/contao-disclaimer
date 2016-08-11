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


use HeimrichHannot\Disclaimer\DisclaimerArchiveModel;

class ModuleBackend extends \Backend
{
	/**
	 * Return all disclaimer templates as array
	 *
	 * @return array
	 */
	public function getDisclaimerTemplates()
	{
		return $this->getTemplateGroup('disclaimer_');
	}
	
	
	public function getDisclaimerArchives(\DataContainer $dc)
	{
		$arrOptions = array();
		
		if (!\BackendUser::getInstance()->isAdmin && !is_array(\BackendUser::getInstance()->disclaimers))
		{
			return $arrOptions;
		}
		
		if(($objArchives = DisclaimerArchiveModel::findAll()) === null)
		{
			return $arrOptions;
		}
		
		while ($objArchives->next())
		{
			if (\BackendUser::getInstance()->hasAccess($objArchives->id, 'disclaimers'))
			{
				$arrOptions[$objArchives->id] = $objArchives->title;
			}
		}
		
		return $arrOptions;
	}
	
}