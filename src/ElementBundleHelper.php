<?php
/**
 * This file contains trait
 *
 * @package    ObjectOrder
 * @license    http://opensource.org/licenses/MIT  GNU Public License
 * @author     trzczy <trzczy@gmail.com>
 */
namespace Trzczy\Helpers;
use stdClass;

/**
 * A trait contains methods for handling objects that are to be sorted
 *
 * The trait to build the final order of objects that are to be sorted
 */
trait ElementBundleHelper
{
    /**
     * Returns final array of arrays of shared elements
     *     *
     * @example $elementBundle param may look like this
     *
     * '[
     *      {
     *          "method":"Zbigniew",
     *          "input":"Herbert",
     *          "arg1":24,
     *          "arg2":"abc"
     *      },
     *      {
     *          "method":"Frank",
     *          "input":"Herbert",
     *          "former":[
     *              {"arg2":"abc"}
     *          ]
     *      },
     *      {
     *          "method":"Edith",
     *          "input":"Stein",
     *          "former":[
     *              {"method":"Frank"},
     *              {"arg2":"abc"}
     *          ]
     *      },
     *      {
     *          "method":"Ernest",
     *          "input":"Hemingway",
     *          "former":[
     *              {"input":"Herbert"},
     *              {"method":"Edith"}
     *          ]
     *      }
     * ]'
     *
     * @param string $elementBundle
     * @return array
     */
    public function order(string $elementBundle):array
    {
        $elementBundle = json_decode($elementBundle);
        $elementBundleRepresentation = $this->represent($elementBundle);
        $sortedElementBundleRepresentation = $this->orderStringRepresentation($elementBundleRepresentation);
        return array_map(function ($nestedArray) use ($elementBundle) {
            return array_map(
                function ($hash) use ($elementBundle) {
                    return $this->hash2stdClassObject($hash, $elementBundle);
                },
                $nestedArray
            );
        }, $sortedElementBundleRepresentation);
    }

    /**
     * Returns array of objects of string properties
     *
     * Returned array has objects of properties of values that are object hash representations.
     *
     * @param array $elementBundle
     * @return array
     */
    private function represent(array $elementBundle):array
    {
        $elementBundleRepresentation = [];
        foreach ($elementBundle as $element) {
            $elementObjectRepresentation = new stdClass;
            $elementObjectRepresentation->id = spl_object_hash($element);
            $former = array_filter($element->former??[]);
            if ($former) {
                $formerRepresentation = [];
                foreach ($former as $formerOne) {
                    foreach ($elementBundle as $element2) {
                        if (call_user_func_array(array($this, 'isLike'), [$formerOne, $element2])) {
                            $formerRepresentation[] = spl_object_hash($element2);
                        }
                    }
                }
                $elementObjectRepresentation->former = $formerRepresentation;
            }
            $elementBundleRepresentation[] = $elementObjectRepresentation;
        }
        return $elementBundleRepresentation;
    }

    /**
     * Extract stdClass object from hash
     *
     * @param string $hash
     * @param array $elementBundle
     * @return mixed|null|stdClass
     */
    private function hash2stdClassObject(string $hash, array $elementBundle):stdClass
    {
        foreach ($elementBundle as $stdClassObject) {
            if (spl_object_hash($stdClassObject) === $hash) {
                unset ($stdClassObject->former);
                return $stdClassObject;
            }
        }
        return null;
    }
}