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


class Hooks
{
	public function postDownloadHook($strFile)
	{
		$objModel = \FilesModel::findByPath($strFile);
		
		if($objModel === null || !$objModel->disclaimer)
		{
			return;
		}
		
		if(($objDisclaimerArchive = DisclaimerArchiveModel::findPublishedById($objModel->disclaimer)) === null)
		{
			return;
		}
		
		return Disclaimer::show($objDisclaimerArchive, $objModel->path);
	}
}