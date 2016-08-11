<?php

namespace HeimrichHannot\Disclaimer;

class DisclaimerArchiveModel extends \Model
{

	protected static $strTable = 'tl_disclaimer_archive';
	
	
	/**
	 * Find a published disclaimer archives by its ID
	 *
	 * @param integer $intId      The disclaimer ID
	 * @param array   $arrOptions An optional options array
	 *
	 * @return \DisclaimerArchiveModel|null The model or null if there is no disclaimer archive
	 */
	public static function findPublishedById($intId, array $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = array("$t.id=?");
		
		if (!BE_USER_LOGGED_IN)
		{
			$time = \Date::floorToMinute();
			$arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
		}
		
		return static::findOneBy($arrColumns, $intId, $arrOptions);
	}
}