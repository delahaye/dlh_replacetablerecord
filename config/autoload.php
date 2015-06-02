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
 * Register the classes
 */
ClassLoader::addClasses(array
(
    // Classes
    'Delahaye\ReplaceTableRecord\ReplaceRecord'     => 'system/modules/dlh_replacetablerecord/classes/Delahaye/ReplaceTableRecord/ReplaceRecord.php',
    'Delahaye\ReplaceTableRecord\ReplaceInsertTags' => 'system/modules/dlh_replacetablerecord/classes/Delahaye/ReplaceTableRecord/ReplaceInsertTags.php',
));