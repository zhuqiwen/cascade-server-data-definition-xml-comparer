<?php

namespace Edu\IU\RSB\XmlComparer\Exceptions;

use Edu\IU\RSB\XmlComparer\Traits\Traits;

class XmlComparerException extends \Exception{
    protected string $path = "";

    protected string $nodeString = "";

    use Traits;

    public function getPath():string
    {
        return $this->path;
    }

    public function getNodeString():string
    {
        return $this->nodeString;
    }

    public function setPath(\SimpleXMLElement $xmlObj):void
    {
        $this->path .= $xmlObj->getName();
        $this->path .= '==>>';
    }


    public function setNodeString(\SimpleXMLElement $xmlObj):void
    {
        $this->nodeString .= $this->constructNodeString($xmlObj);
        $this->nodeString .= "==>>";
    }

}