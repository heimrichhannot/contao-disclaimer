<?php

namespace HeimrichHannot\Disclaimer;

class DisclaimerModel extends \Model
{

    protected static $strTable = 'tl_disclaimer';

    /**
     * Find a published disclaimer by its language and pid
     *
     * @paran string  $strLanguage The disclaimer language
     *
     * @param integer $pid        The disclaimer archive id
     * @param array   $arrOptions An optional options array
     *
     * @return \DisclaimerModel|null The model or null if there is no disclaimer
     */
    public static function findPublishedByLanguageAndParent($strLanguage, $pid, array $arrOptions = array())
    {
        $t          = static::$strTable;
        $arrColumns = array("$t.language = ? AND $t.pid=?");

        if (!BE_USER_LOGGED_IN)
        {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        $objDisclaimer = static::findOneBy($arrColumns, array($strLanguage, $pid), $arrOptions);

        // try to find fallback page
        if ($objDisclaimer === null)
        {
            return static::findPublishedByFallbackAndParent($pid, $arrOptions);
        }

        return static::findOneBy($arrColumns, array($strLanguage, $pid), $arrOptions);
    }

    /**
     * Find a published disclaimer by pid and fallback
     *
     * @param integer $pid        The disclaimer archive id
     * @param array   $arrOptions An optional options array
     *
     * @return \DisclaimerModel|null The model or null if there is no disclaimer
     */
    public static function findPublishedByFallbackAndParent($pid, array $arrOptions = array())
    {
        $t          = static::$strTable;
        $arrColumns = array("$t.fallback=1 AND $t.pid=?");

        if (!BE_USER_LOGGED_IN)
        {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        return static::findOneBy($arrColumns, array($pid), $arrOptions);
    }

    /**
     * Find a published disclaimer by its ID
     *
     * @param integer $intId      The disclaimer ID
     * @param array   $arrOptions An optional options array
     *
     * @return \DisclaimerModel|null The model or null if there is no disclaimer
     */
    public static function findPublishedById($intId, array $arrOptions = array())
    {
        $t          = static::$strTable;
        $arrColumns = array("$t.id=?");

        if (!BE_USER_LOGGED_IN)
        {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        return static::findOneBy($arrColumns, $intId, $arrOptions);
    }
}