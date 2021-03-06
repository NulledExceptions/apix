<?php

/**
 *
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license     http://opensource.org/licenses/BSD-3-Clause  New BSD License
 *
 */

namespace Apix\Fixtures;

/**
 * Class title
 *
 * Class description 1st line
 * Class description 2nd line
 *
 * @param int $classNamedInteger an integer
 * @api_public          true
 * @api_version         1.0
 * @api_permission      admin
 * @api_randomName      classRandomValue
 * @randomGrouping      a group value
 * @randomGrouping      another group value
 */
class DocbookClass
{

    /**
     * Method one title
     *
     * Method description 1st line
     * Method description 2nd line
     *
     * @param  int     $namedInteger an integer
     * @param  string  $namedString  a string
     * @param  boolean $namedBoolean a boolean
     * @param  array   $optional     something optional (here an array)
     * @return array   result
     * @api_public       true
     * @api_version      1.0
     * @api_permission   admin
     * @api_randomName   methodRandomValue
     * @rndGrouping      a group value
     * @rndGrouping      another group value
     * @api_link OPTIONS .*\.foo\.bar
     */
    public function methodNameOne($namedInteger, $namedString, $namedBoolean, array $optional=null)
    {
        return array($namedInteger, $namedString, $namedBoolean, $optional);
    }

    /**
     * Method two title
     *
     * Method two description 1st line
     * Method two description 2nd line
     *
     * @param string $arg1     an integer
     * @param array  $required something required
     * @param array  $optional something optional
     * @api_public false
     * @api_version 1.0
     */
    public static function methodNameTwo($arg1, $required, $optional=null)
    {
        return array($arg1, $optional);
    }

}
