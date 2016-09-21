<?php
/**
 * This file contains an interface for the main application class
 *
 * @package    ObjectOrder
 * @license    http://opensource.org/licenses/MIT  GNU Public License
 * @author     trzczy <trzczy@gmail.com>
 */
namespace Trzczy\Helpers;


use stdClass;

/**
 * An interface of the main application class
 *
 * The interface determines the class to declare a method for checking that
 * an element description indicates the element.
 *
 * The class prepares final array of arrays of sorted validation rule objects
 */
interface Elements
{
    /**
     * Check if $tested object contains $description object property values
     *
     * @param stdClass $description
     * @param stdClass $tested
     * @return bool
     */
    function isLike(stdClass $description, stdClass $tested):bool;
}