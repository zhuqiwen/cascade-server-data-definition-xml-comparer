<?php

namespace Edu\IU\RSB\XmlComparer\Traits;

trait Traits{

    public array $skipAttributes = [];

    public function convertAttributesToArray(\SimpleXMLElement $xmlObj):array
    {
        $attributes = [];

        foreach ($xmlObj->attributes() as $k => $v){
            if(!in_array($k, $this->skipAttributes)){
                $attributes[$k] = "$v";
            }

        }

        return $attributes;
    }

    public function convertAllChildrenToArray(\SimpleXMLElement $xmlObj):array
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


    public function constructNodeString(\SimpleXMLElement $xmlObj):string
    {
        $nodeName = $xmlObj->getName();
        $attributesArray = $this->convertAttributesToArray($xmlObj);

        $nodeString = "<" . $nodeName;
        $nodeString .= " ";

        $attributesString = "";
        foreach ($attributesArray as $k => $v){
            $attributesString .= " ";
            $attributesString .= $k . '="' . $v . '"';
            $attributesString .= " ";
        }
        $nodeString .= $attributesString;
        $nodeString .= "/>";

        return $nodeString;

    }
}