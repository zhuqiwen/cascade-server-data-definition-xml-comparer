<?php

namespace Edu\IU\RSB\XmlComparer\Exceptions;


use Edu\IU\RSB\XmlComparer\Traits\Traits;

class AttributesNotMatchException extends XmlComparerException {


    use Traits;
    public function __construct(\SimpleXMLElement $xmlObjOld, \SimpleXMLElement $xmlObjNew, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = $this->catchAttributesDifference($xmlObjOld, $xmlObjNew);
        parent::__construct($message, $code, $previous);
    }

    public function catchAttributesDifference(\SimpleXMLElement $xmlObjOld, \SimpleXMLElement $xmlObjNew):string
    {
        $oldHasButNewDont = array_diff_assoc($this->convertAttributesToArray($xmlObjOld), $this->convertAttributesToArray($xmlObjNew));
        $msg = "[";
        foreach ($oldHasButNewDont as $attributeName => $v){
            $msg .= $xmlObjNew->getName() . " doesn't have '$attributeName' with value of '$v'; ";
        }
        $msg .= "]";
        $msg = " mismatched attributes: " . $msg;

        return $msg;
    }
}