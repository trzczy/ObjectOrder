<?php
/**
 * This file contains the main class of the application
 *
 * @package    ObjectOrder
 * @license    http://opensource.org/licenses/MIT  GNU Public License
 * @author     trzczy <trzczy@gmail.com>
 */
namespace Trzczy\Helpers;

use stdClass;

/**
 * The main class of the application
 *
 * The class prepares final array of arrays of sorted validation rule objects
 */
class Rules implements Elements
{
    /**
     * @var mixed Should contain nested associated array or json string of such an array.
     */

    use ElementBundleHelper;
    use StringRepresentationHelper;

    /**
     * Check if $tested object contains $description object property values
     *
     * @param stdClass $description
     * @param stdClass $tested
     * @return bool
     */
    function isLike(stdClass $description, stdClass $tested):bool
    {
        $assocArrayArgs = array_map(
            function ($arg) {
                return get_object_vars($arg);
            },
            func_get_args()
        );
        return !(bool)call_user_func_array('array_diff_assoc', $assocArrayArgs);
    }
}