<?php

namespace Itvisionsy\LaravelExtras\Presenter;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Description of PresenterCollection
 *
 * @author muhannad
 */
class PresenterCollection {

    protected $_collection;

    public function __construct(Collection $collection) {
        $this->_collection = $collection;
    }

    public function get($key, $default = null) {
        $result = $this->_collection->get($key, $default);
        if ($result instanceof Model && $result instanceof PresenterAwareInterface) {
            $result = $result->presenter;
        }
        return $result;
    }

    public function __get($name) {
        return $this->_collection->$name;
    }

    public function __set($name, $value) {
        return $this->_collection->$name = $value;
    }

    public function __call($name, $arguments) {
        return call_user_func_array([$this->_collection, $name], $arguments);
    }

    public static function __callStatic($name, $arguments) {
        return call_user_func(['\\Illuminate\\Database\\Eloquent\\Collection', $name], $arguments);
    }

}
