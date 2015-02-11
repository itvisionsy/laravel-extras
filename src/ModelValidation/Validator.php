<?php

namespace ItvisionSy\LaravelExtras\ModelValidation;

use Illuminate\Validation\Validator as CoreValidator;
use function app;

/**
 * Description of Validator
 *
 * @author muhannad
 * 
 * @method boolean|CoreValidator validate(array $data)
 * @property-read array $rules
 * @property-read CoreValidator $validator
 * @property array $data
 */
abstract class Validator {

    protected $rules = [];
    protected $data = [];
    protected $validator = null;

    public static function make(array $data) {
        return new static($data);
    }

    public function __construct(array $data = []) {
        $this->data = $data;
    }

    public function data(array $data = null) {
        if ($data) {
            $this->data = (array) $data;
            return $this;
        } else {
            return $this->data;
        }
    }

    protected function _validate() {
        $this->validator = new CoreValidator(app()->make('translator'), $this->data, $this->rules());
        return !$this->validator->fails();
    }

    protected function rules() {
        return $this->rules;
    }

    public function __call($name, $arguments) {
        switch ($name) {
            case 'validate':
                return $this->_validate();
                break;
            default:
                user_error("Method `$name` was not found!", E_USER_ERROR);
        }
    }

    public static function __callStatic($name, $arguments) {
        switch ($name) {
            case 'validate':
                $validator = static::make(@$arguments[0]);
                return $validator->validate()? : $validator->validator;
            default:
                user_error("Static method `$name` was not found!", E_USER_ERROR);
        }
    }

    public function __set($name, $value) {
        switch ($name) {
            case 'data':
                $this->data($value);
            default:
                user_error("Property `$name` was not found or is not writable!", E_USER_ERROR);
        }
    }

    public function __get($name) {
        switch ($name) {
            case 'validator':
                return $this->validator();
            case 'rules':
                return $this->rules();
            case 'data':
                return $this->data;
            default:
                user_error("Property `$name` was not found!", E_USER_ERROR);
        }
    }

}
