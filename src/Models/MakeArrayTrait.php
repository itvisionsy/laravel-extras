<?php

namespace ItvisionSy\LaravelExtras\Models;

use ReflectionClass;

trait MakeArrayTrait {

    public static function _fillables() {
        static $fillables = null;
        if ($fillables === null) {
            $new = new static();
            $reflectionClass = new ReflectionClass($new);
            $reflectionProperty = $reflectionClass->getProperty('fillable');
            $reflectionProperty->setAccessible(true);
            $fillables = $reflectionProperty->getValue($new);
        }
        return $fillables;
    }
    
    public static function _attributes() {
        static $attributes = null;
        if ($attributes === null) {
            $new = new static();
            $attributes = $new->toArray();
        }
        return $attributes;
    }

    protected static function _attributeKey($attribute) {
        $fillables = static::_fillables();
        return array_search($attribute, $fillables);
    }

    public static function makeArray() {
        $array = [];
        $args = func_get_args();
        foreach (static::_fillables() as $index => $key) {
            $value = @$args[$index] !== null ? @$args[$index] : (array_get(static::_attributes(), $key) !== null ? array_get(static::_attributes(), $key) : null);
            $array[$key] = $value;
        }
        return $array;
    }

}
