<?php
/**
 * This file contains trait
 *
 * @package    ObjectOrder
 * @license    http://opensource.org/licenses/MIT  GNU Public License
 * @author     trzczy <trzczy@gmail.com>
 */
namespace Trzczy\Helpers;

/**
 * A trait contains methods for handling objects with id string properties.
 *
 * The trait to build the final order of string elements
 */
trait StringRepresentationHelper
{
    /**
     * Sort array of objects of id string properties in the desired order
     *
     * @example $data param may look like this but should not be encoded
     * '[
     *      {
     *          "id":"ghi0",
     *          "former":
     *              ["5ab","w2xy3"]
     *      },
     *      {
     *          "id":"5ab"
     *      },
     *      {
     *          "id":"xyz3"
     *      },
     *      {
     *          "id":"ghi1",
     *          "former":
     *              ["ghi0"]
     *      },
     *      {
     *          "id":"w2xy3"
     *      }
     * ]'
     *
     * @param $data
     * @return array
     */
    private function orderStringRepresentation(array $data):array
    {
        $formerTotal = [];
        while (0 < count($data)) {
            $former = [];
            $newElementBundle = [];
            foreach ($data as $element) {
                $isSuccessor = $this->isSuccessor($element);
                $isNotSuccessor = !$isSuccessor;
                if ($isSuccessor) $newElementBundle[] = $element;
                if ($isNotSuccessor) $former[] = $element->id;
            }
            $formerTotal[] = $former;

            unset($data);
            $data = $newElementBundle;

            foreach ($former as $id) {
                $data = $this->removeNestedFormer($data, $id);
            }
        }
        return $formerTotal;
    }

    /**
     * Check if an element is to be deferred
     *
     * @param $element
     * @return bool
     */
    private function isSuccessor($element):bool
    {
        return (
            isset($element->former)
            and
            array_filter($element->former)
        );
    }

    /**
     * Remove former values from given $elementBundle
     *
     * @param array $elementBundle
     * @param string $formerToRemove
     * @return array
     */
    private function removeNestedFormer(array $elementBundle, string $formerToRemove):array
    {
        $newElementBundle = [];
        foreach ($elementBundle as $element) {
            if (isset($element->former)) {
                $newElementFormer = array_filter(
                    $element->former,
                    function ($testedElementIdent) use ($formerToRemove) {
                        return ($testedElementIdent !== $formerToRemove);
                    }
                );
                if (!$newElementFormer) {
                    unset($element->former);
                } else {
                    $element->former = $newElementFormer;
                }
            }
            $newElementBundle[] = $element;
        }
        return $newElementBundle;
    }
}