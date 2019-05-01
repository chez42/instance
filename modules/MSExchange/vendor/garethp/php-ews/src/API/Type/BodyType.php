<?php

namespace garethp\ews\API\Type;

use garethp\ews\API\Type;

/**
 * Class representing BodyType
 *
 *
 * XSD Type: BodyType
 *
 * @method string getBodyType()
 * @method BodyType setBodyType(string $bodyType)
 */
class BodyType extends Type
{

    /**
     * @var string
     */
    protected $bodyType = null;

    public function __construct($text = '', $type = 'HTML')
    {
        $this->_ = $text;
        $this->bodyType = $type;
    }
}
