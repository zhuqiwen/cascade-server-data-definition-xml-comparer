<?php

namespace Edu\IU\RSB\XmlComparer\Exceptions;


use Edu\IU\RSB\XmlComparer\Traits\Traits;

class ChildrenCountsNotMatchException extends XmlComparerException {

    use Traits;
    public function __construct(\SimpleXMLElement $xmlObjOld, \SimpleXMLElement $xmlObjNew, int $code = 0, ?Throwable $previous = null)
    {
        $childrenCountOld = sizeof($this->convertAllChildrenToArray($xmlObjOld));
        $childrenCountNew = sizeof($this->convertAllChildrenToArray($xmlObjNew));


        $message = "Old: " .  $this->constructNodeString($xmlObjOld) . " has " . $childrenCountOld . " children";
        $message .= " while new: " .  $this->constructNodeString($xmlObjNew) . " has " . $childrenCountNew . " children";

        parent::__construct($message, $code, $previous);
    }






}