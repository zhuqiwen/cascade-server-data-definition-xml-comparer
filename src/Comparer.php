<?php

namespace Edu\IU\RSB\XmlComparer;

use Edu\IU\RSB\XmlComparer\Exceptions\AttributesNotMatchException;
use Edu\IU\RSB\XmlComparer\Exceptions\ChildMissingException;
use Edu\IU\RSB\XmlComparer\Exceptions\ChildrenCountsNotMatchException;
use Edu\IU\RSB\XmlComparer\Exceptions\SameNameChildrenCountNotMatchException;
use Edu\IU\RSB\XmlComparer\Exceptions\XmlComparerException;
use Edu\IU\RSB\XmlComparer\Traits\Traits;

class Comparer{

    public array $reasons = [];
    public bool $compareResult;

    public string $parentPath = '';
    public \SimpleXMLElement $xmlObjOld;
    public \SimpleXMLElement $xmlObjNew;
    use Traits;

    public function __construct(\SimpleXMLElement $xmlObjOld, \SimpleXMLElement $xmlObjNew, array $skipAttributes = [])
    {
        $this->xmlObjOld = $xmlObjOld;
        $this->xmlObjNew = $xmlObjNew;
        $this->skipAttributes = $skipAttributes;
    }

    public function compare():void
    {
        try {
            $this->compareResult = $this->simpleXmlElementsAreEqual($this->xmlObjOld, $this->xmlObjNew);
        }catch (XmlComparerException $e) {
            $this->compareResult = false;
            $this->reasons['reason'] = $e->getMessage();
            $this->reasons['parentPath'] = $this->parentPath;
        }
    }

    /**
     * @throws AttributesNotMatchException
     * @throws ChildrenCountsNotMatchException
     * @throws SameNameChildrenCountNotMatchException
     * @throws ChildMissingException
     * @throws XmlComparerException
     */
    public function simpleXmlElementsAreEqual(\SimpleXMLElement $xmlObjOld, \SimpleXMLElement $xmlObjNew):true
    {
        //if attributes match
        if(!$this->equalAttributes($xmlObjOld, $xmlObjNew)){
            throw new AttributesNotMatchException($xmlObjOld, $xmlObjNew);
        }
        //if children counts match
        if(!$this->equalChildrenCounts($xmlObjOld, $xmlObjNew)){
            throw new  ChildrenCountsNotMatchException($xmlObjOld, $xmlObjNew);
        }

        $oldChildren = $this->convertAllChildrenToArray($xmlObjOld);
        $newChildren = $this->convertAllChildrenToArray($xmlObjNew);
        //check children details
        foreach ($oldChildren as $oldChildName => $oldChildrenArray) {
            if(!isset($newChildren[$oldChildName])){
                $nodeString = $this->constructNodeString($xmlObjNew);
                throw new ChildMissingException($nodeString . " doesn't have child of '$oldChildName'");
            }

            $oldCount = sizeof($oldChildrenArray);
            $newCount = sizeof($newChildren[$oldChildName]);
            if($oldCount > $newCount){
                throw new SameNameChildrenCountNotMatchException($xmlObjOld, $oldChildName, $oldCount, $newCount);
            }

            $newChildrenArray = $newChildren[$oldChildName];
            foreach ($oldChildrenArray as $oldChild){
               $matchFound = false;
               $results = [];
               $paths = [];
                foreach ($newChildrenArray as $index => $newChild){
                    try {
                        if ($this->simpleXmlElementsAreEqual($oldChild, $newChild) === true){
                            $matchFound = true;
                            unset($newChildrenArray[$index]);
                        }

                    }catch (AttributesNotMatchException | ChildMissingException | ChildrenCountsNotMatchException | SameNameChildrenCountNotMatchException $e){
                        $results[] = $e->getMessage();
                        $paths[] = $this->constructNodeString($xmlObjOld);
                    }
               }
               if(!$matchFound){
                   $this->parentPath = array_unique($paths)[0];
                   echo $this->parentPath . PHP_EOL;
                   $results = array_unique($results);
                   throw new XmlComparerException($results[0]);
               }
            }



        }


        return true;

    }

    public function equalAttributes(\SimpleXMLElement $xmlObjOld, \SimpleXMLElement $xmlObjNew):bool
    {
        $oldAttributesArray = $this->convertAttributesToArray($xmlObjOld);
        $newAttributesArray = $this->convertAttributesToArray($xmlObjNew);

        return $oldAttributesArray == $newAttributesArray;
    }

    public function equalChildrenCounts(\SimpleXMLElement $xmlObjOld, \SimpleXMLElement $xmlObjNew):bool
    {
        $oldChildren = $this->convertAllChildrenToArray($xmlObjOld);
        $newChildren = $this->convertAllChildrenToArray($xmlObjNew);

        return sizeof($oldChildren) == sizeof($newChildren);
    }

}