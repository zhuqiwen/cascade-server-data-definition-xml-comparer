<?php

namespace Edu\IU\RSB\XmlComparer\Exceptions;

class RootNameNotMatchException extends XmlComparerException{

    public function __construct(\SimpleXMLElement $xmlObjOld, \SimpleXMLElement $xmlObjNew, int $code = 0, ?Throwable $previous = null)
    {
        $message = 'root names are different: ' . $xmlObjOld->getName() . '<-->' . $xmlObjNew->getName();
        parent::__construct($message, $code, $previous);
    }
}