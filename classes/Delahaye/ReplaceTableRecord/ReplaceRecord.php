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

use \Database;
use \Input;
use \Controller;


/**
 * Class ReplaceRecord
 *
 * Provides a method for replacing a table record (e.g. MetaModel) by saving a generator form
 *
 * @package Delahaye\ReplaceTableRecord
 */
class ReplaceRecord
{
    protected static $objDb;
    protected static $strTable;
    protected static $intNewId;
    protected static $intOldId;


    /**
     * Replaces the id of a new stored record with a given one if a replace is done
     *
     * @param $arrSubmitted
     * @param $arrData
     * @param $arrFiles
     * @param $arrLabels
     * @param $objThis
     */
    public function replaceId($arrSubmitted, $arrData, $arrFiles, $arrLabels, $objThis)
    {
        // only if the form values are stored
        if (!$objThis->storeValues) {
            return;
        }

        static::$objDb    = Database::getInstance();
        static::$strTable = $objThis->targetTable;
        static::$intOldId = $arrSubmitted['id'];

        // get the last inserted Id
        $tmp = static::$objDb->prepare('select id from ' . static::$strTable);
        static::$intNewId =  $tmp->insertId;

        // HOOK: prevent from editing
        if (isset($GLOBALS['TL_HOOKS']['dlh_ReplaceTableRecord']) && is_array($GLOBALS['TL_HOOKS']['dlh_ReplaceTableRecord']))
        {
            foreach ($GLOBALS['TL_HOOKS']['dlh_ReplaceTableRecord'] as $callback)
            {
                $this->import($callback[0]);
                $isAllowed = $this->$callback[0]->$callback[1]($arrSubmitted, $arrData, $arrFiles, $arrLabels, $objThis, static::$intOldId, static::$intNewId);

                // delete new entry to leave everything as it is
                if(!$isAllowed) {
                    $this->deleteRecord(static::$intNewId);

                    return;
                }
            }
        }

        // if an existing id is provided by the form
        if ($arrSubmitted['id']) {
            // only an id provided: delete records
            if (count($arrSubmitted) == 1) {
                $this->deleteRecord(static::$intOldId);
                $this->deleteRecord(static::$intNewId);

                // reload without id
                $this->reloadWithoutId();

            // update the provided fields of the old record
            } else {
                // id is not updated
                unset($arrSubmitted['id']);

                // update old record
                $this->updateRecord($arrSubmitted);

                // delete new record
                $this->deleteRecord(static::$intNewId);

                return;
            }

        // a record is deleted
        } elseif (count($arrSubmitted) == 1) {
            // delete new record
            $this->deleteRecord(static::$intNewId);

            if (Input::get('id')) {
                // reload with new id
                $this->reloadWithId();
            }

            return;

        // a new record is stored, load it
        } else {
            // reload with new id
            $this->reloadWithId();
        }
    }


    /**
     * Delete a database record
     *
     * @param $intId
     * @return mixed
     */
    protected function deleteRecord($intId)
    {
        return static::$objDb->prepare("DELETE FROM " . static::$strTable . " WHERE id=?")->execute($intId);
    }


    /**
     * Update a database record
     *
     * @param $arrSubmitted
     */
    protected function updateRecord($arrSubmitted)
    {
        static::$objDb->prepare("UPDATE " . static::$strTable . " %s WHERE id=?")->set($arrSubmitted)->execute(static::$intOldId);

        return;
    }


    /**
     * Reload the page with the given id on entering a new record
     */
    protected function reloadWithId()
    {
        global $objPage;
        Controller::redirect(Controller::generateFrontendUrl($objPage->row(), '/id/' . static::$intNewId));
    }


    /**
     * Reload the page without a selected id
     */
    protected function reloadWithoutId()
    {
        global $objPage;
        Controller::redirect(Controller::generateFrontendUrl($objPage->row()));
    }

}