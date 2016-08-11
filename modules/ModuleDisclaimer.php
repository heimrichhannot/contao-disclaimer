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


class ModuleDisclaimer extends \Module
{
	protected $strTemplate = 'mod_disclaimer';
	
	protected $objDisclaimer;
	
	protected $arrConfig = array();
	
	public function generate()
	{
		if (TL_MODE == 'BE') {
			$objTemplate           = new \BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD'][$this->type][0]) . ' ###';
			$objTemplate->title    = $this->headline;
			$objTemplate->id       = $this->id;
			$objTemplate->link     = $this->name;
			$objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;
			
			return $objTemplate->parse();
		}
		
		if(($this->objDisclaimer = DisclaimerModel::findPublishedByLanguageAndParent($GLOBALS['TL_LANGUAGE'], Disclaimer::getDisclaimer())) === null)
		{
			return '';
		}
		
		$this->arrConfig = $this->getModel()->row();
		$this->arrConfig['disclaimer_archives'] = deserialize($this->arrConfig['disclaimer_archives'], true);
		
		if(!in_array($this->objDisclaimer->pid, $this->arrConfig['disclaimer_archives']))
		{
			return '';
		}
		
		return parent::generate();
	}
	
	
	protected function compile()
	{
		$this->Template->disclaimer = Disclaimer::parse($this->objDisclaimer, $this->arrConfig);
	}
}