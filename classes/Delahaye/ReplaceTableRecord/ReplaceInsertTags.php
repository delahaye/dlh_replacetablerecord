<?php

/**
 * ReplaceTableRecord
 * Extension for Contao Open Source CMS (contao.org)
 *
 * Copyright (c) 2015 de la Haye
 *
 * @author  Christian de la Haye
 * @link    http://delahaye.de
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


namespace Delahaye\ReplaceTableRecord;

use \Controller;
use \Database;
use \PageModel;
use \Input;


/**
 * Class ReplaceInsertTags
 *
 * Replaces insert tags which are used to load table data into generator forms via standard value
 *
 * @package Delahaye\ReplaceTableRecord
 */
class ReplaceInsertTags extends Controller
{

    /**
     * Replaces an insert tag with the record data from the table
     * Insert tags like {{tabledata::|field|::|table|::|parameterfield|::|urlparameter|}}
     * Insert tags for the fixed field 'id' like
     *    {{tabledata::id::|table|::|parameterfield|::|urlparameter|}}
     * or {{tabledata::id::|table|::|parameterfield|::|urlparameter|::member::|memberfield|}}
     * or {{tabledata::id::|table|::|parameterfield|::|urlparameter|::groups::|groupsfield|::|groups like 1,2,3 or serialized|}}
     *
     * @param $strTag
     * @return bool|string
     */
    public function replaceTag($strTag)
    {
        $arrTag = trimsplit('::', $strTag);

        // key 'tabledata'
        if($arrTag[0] != 'tabledata') {
            // divert to other insert tags
            return false;
        }

        // get table record only if all parameters are present
        if(!$arrTag[1] || !$arrTag[2] || !$arrTag[3] || !$arrTag[4]) {
            // return empty string
            return '';
        }

        // check if record is restricted to a member or a membergroup
        $isRestricted = ($arrTag[1] == 'id' && ($arrTag[5] == 'member' || $arrTag[5] == 'groups')) ? true : false;

        // get record data
        $objResult = Database::getInstance()->prepare("SELECT " . $arrTag[1] . ($isRestricted ? ',' . $arrTag[6] : '') . " FROM " . $arrTag[2] . " WHERE " . $arrTag[3] . "=?")->execute(Input::get($arrTag[4]));

        // no record found
        if(!$objResult->$arrTag[1]) {
            // if is id then return 404 page
            if($arrTag[1] == 'id' && \Input::get('id')) {
                global $objPage;
                $objP = PageModel::find404ByPid($objPage->rootId);
                $this->redirect($this->generateFrontendUrl($objP->row()));
            }

            // else return empty string
            return '';
        }

        // check if record is restricted to a member or a membergroup
        if($isRestricted) {

            $isAllowed = false;

            // get frontend user
            $this->import('FrontendUser', 'User');

            switch($arrTag[5]) {
                // check for member groups - doesn't work in MetaModels etc. because of data in spread tables
                case 'groups':
                    $arrGroups1 = deserialize($objResult->$arrTag[6]);
                    $arrGroups1 = is_array($arrGroups1) ? $arrGroups1 : explode(',', $objResult->$arrTag[6]);
                    $arrGroups1 = is_array($arrGroups1) ? (!$arrGroups1[0] ? array(): $arrGroups1) : array();

                    $arrGroups2 = explode(',', $arrTag[7]);
                    $arrGroups2 = is_array($arrGroups2) ? (!$arrGroups2[0] ? array(): $arrGroups2) : array();

                    $isAllowed = FE_USER_LOGGED_IN && count(array_intersect($this->User->groups, array_intersect($arrGroups1, $arrGroups2))) > 0 ? true : false;
                    break;

                // check for a member
                default:
                    $isAllowed = FE_USER_LOGGED_IN && $this->User->id == $objResult->$arrTag[6] ? true : false;
                    break;
            }

            // record can't be loaded - redirect to the blank form
            if(!$isAllowed) {
                global $objPage;
                $this->redirect($this->generateFrontendUrl($objPage->row()));
            }
        }

        // pre-fill form field
        return $objResult->$arrTag[1];
    }

}