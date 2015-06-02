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


/**
 * Register hook
 */

$GLOBALS['TL_HOOKS']['processFormData'][]   = array('Delahaye\ReplaceTableRecord\ReplaceRecord', 'replaceId');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('Delahaye\ReplaceTableRecord\ReplaceInsertTags', 'replaceTag');