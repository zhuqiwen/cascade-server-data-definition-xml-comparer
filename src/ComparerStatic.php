<?php

namespace Edu\IU\RSB\XmlComparer;


class ComparerStatic{


    /**
     * STATIC FUNCTIONS
     */




    public static function convertAttributesToArrayStatic(\SimpleXMLElement $xmlObj, array $skipAttributes):array
    {
        $attributes = [];

        foreach ($xmlObj->attributes() as $k => $v){
            if (!in_array($k, $skipAttributes)){
                $attributes[$k] = "$v";
            }

        }

        return $attributes;
    }

    public static function attributesAreEqual(\SimpleXMLElement $xmlObjOld, \SimpleXMLElement $xmlObjNew, array $skipAttributes):bool
    {

        $oldAttributesArray = self::convertAttributesToArrayStatic($xmlObjOld, $skipAttributes);
        $newAttributesArray = self::convertAttributesToArrayStatic($xmlObjNew, $skipAttributes);
        $isIdentical = $oldAttributesArray == $newAttributesArray;

        return $isIdentical;
    }


    public static function catchAttributesDifference(\SimpleXMLElement $xmlObjOld, \SimpleXMLElement $xmlObjNew, array $skipAttributes):string
    {
        $oldHasButNewDont = array_diff_assoc(self::convertAttributesToArrayStatic($xmlObjOld, $skipAttributes), self::convertAttributesToArrayStatic($xmlObjNew, $skipAttributes));
        $msg = "[";
        foreach ($oldHasButNewDont as $attributeName => $v){
            $msg .= $xmlObjNew->getName() . " doesn't have '$attributeName' with value of '$v'; ";
        }
        $msg .= "]";
        $msg = " mismatched attributes: " . $msg;

        return $msg;
    }
    public static function getAllChildrenIntoArray(\SimpleXMLElement $xmlObj):array
    {
        $childrenArray = [];
        foreach ($xmlObj->children() as $child) {
            if (!isset($childrenArray[$child->getName()])){
                $childrenArray[$child->getName()] = [];
            }
            $childrenArray[$child->getName()][] = $child;
        }

        return $childrenArray;

    }

    public static function isHaveSameNumberOfChildren(\SimpleXMLElement $xmlObjOld, \SimpleXMLElement $xmlObjNew):bool
    {
        $oldChildren = self::getAllChildrenIntoArray($xmlObjOld);
        $newChildren = self::getAllChildrenIntoArray($xmlObjNew);

        $isSameSize = sizeof($oldChildren) == sizeof($newChildren);

        return $isSameSize;
    }

    public static function simpleXmlObjsAreEqual(\SimpleXMLElement $xmlObjOld, \SimpleXMLElement $xmlObjNew, array $skipAttributes = []):true|string
    {
        //if root name are same
        if($xmlObjOld->getName() != $xmlObjNew->getName()){
            return 'root names are different: ' . $xmlObjOld->getName() . '<-->' . $xmlObjNew->getName();
        }

        //if attributes don't match, return false and append error message
        if(!self::attributesAreEqual($xmlObjOld, $xmlObjNew, $skipAttributes)){
            return self::catchAttributesDifference($xmlObjOld, $xmlObjNew, $skipAttributes);
        }
        //if children sizes don't match, return false and append error message
        if(!self::isHaveSameNumberOfChildren($xmlObjOld, $xmlObjNew)){
            return "numbers of children doesn't match";
        }

        $oldChildren = self::getAllChildrenIntoArray($xmlObjOld);
        $newChildren = self::getAllChildrenIntoArray($xmlObjNew);


        //if all good, iterate through children to compare one level down
        //use old as the standard
        foreach ($oldChildren as $oldChildName => $oldChildrenArray){
            // if in new, one child name in old doesn't exist
            if(!isset($newChildren[$oldChildName])){
                return $xmlObjNew->getName() . "doesn't have child of '$oldChildName'";
            }
            // if a child name does exist in both old and new, check if the numbers match
            // return error message when new has fewer children than old
            if(sizeof($oldChildrenArray) > sizeof($newChildren[$oldChildName])){
                return "mismatched children count under: " . $xmlObjOld->getName() . "-->" . $oldChildName . "(old: " . sizeof($oldChildrenArray) . " <--> new: " . sizeof($newChildren[$oldChildName]) . ")";
            }
            //if both new and old have children with identical tag name, and the numbers of children are same
            foreach ($oldChildrenArray as $oldChild){
                $sameNewChildFound = false;
                $results = [];
                foreach ($newChildren[$oldChildName] as $index => $newChild){
                    if (($result = self::simpleXmlObjsAreEqual($oldChild, $newChild, $skipAttributes)) === true){
                        $sameNewChildFound = true;
                        unset($newChildren[$oldChildName][$index]);
                    }else{
                        $results[] = $result;
                    }
                }
                if(!$sameNewChildFound){
                    $results = array_unique($results);
                    $results = [$results[0]];
                    $errorMessage = $xmlObjOld->getName();
                    $errorMessage .= "==>>";
                    $errorMessage .=  $results[0];
                    return $errorMessage;
                }
            }
        }

        return true;
    }

}