<?php

namespace garethp\ews\API\Type;

use garethp\ews\API\Type;

/**
 * Class representing TrackingPropertyType
 *
 *
 * XSD Type: TrackingPropertyType
 *
 * @method string getName()
 * @method TrackingPropertyType setName(string $name)
 * @method string getValue()
 * @method TrackingPropertyType setValue(string $value)
 */
class TrackingPropertyType extends Type
{

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var string
     */
    protected $value = null;
}
