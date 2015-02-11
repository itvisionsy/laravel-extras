<?php

namespace ItvisionSy\LaravelExtras\ModelValidation;

/**
 * Description of ValidatedTrait
 *
 * @author muhannad
 */
trait ValidatedTrait {

    protected static function validatorClass() {
        $reflection = new \ReflectionClass(__CLASS__);
        return $reflection->getNamespaceName() . "\\" . $reflection->getShortName() . "Validator";
    }

    public static function validate(array $input) {
        $validatorClass = static::validatorClass();
        return $validatorClass::validate($input);
    }

}
