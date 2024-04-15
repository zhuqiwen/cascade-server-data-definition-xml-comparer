<?php

namespace Edu\IU\RSB\XmlComparer\Exceptions;


class SameNameChildrenCountNotMatchException extends XmlComparerException {

    public function __construct(\SimpleXMLElement $xmlObjOld, string $oldChildName, int $oldCount, int $newCount, int $code = 0, ?Throwable $previous = null)
    {

        $message = "Children counts of '$oldChildName' under " . $this->constructNodeString($xmlObjOld) . " not match: (old) $oldCount <-> (new) $newCount";

        parent::__construct($message, $code, $previous);
    }

}