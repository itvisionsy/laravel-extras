<?php

namespace ItvisionSy\LaravelExtras\Formatter;

/**
 * Description of BaseFormatter
 *
 * @author muhannad
 */
abstract class BaseFormatter {

    protected $object;

    public function __construct($object) {
        $this->object = $object;
    }

    public function __toString() {
        try {
            return $this->format();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    abstract protected function template();

    protected function find($key) {
        return $this->object->$key;
    }

    public function __get($name) {
        return $this->find($name);
    }

    public function __call($name, $arguments) {
        return call_user_func_array([$this->object, $name], $arguments);
    }

    protected function format() {
        $object = $this->object;
        return preg_replace_callback(['/\{\{([a-zA-Z0-9_]+)(\(([0-9a-zA-Z\,_ ]*)\))?(|[a-zA-Z0-9,_\|]+)?\}\}/', '/:([a-zA-Z0-9_]+)/'], function($matches) use (&$object) {
            $key = $matches[1];
            if (count($matches) > 2) {
                if ($matches[2] !== '') {
                    $params = explode(',', $matches[3]);
                    $value = call_user_func_array([$this, $key], $params);
                } else {
                    $value = $this->find($key);
                }
                $modifiers = explode("|", trim($matches[4], '|'));
                $value = apply($value, $modifiers);
            } else {
                $value = $this->find($key);
            }
            return $value;
        }, $this->template());
    }

}
