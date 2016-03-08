<?php

namespace ItvisionSy\LaravelExtras\Presenter;

use BadFunctionCallException;
use Countable;
use ArrayAccess;
use ArrayIterator as BaseArrayIterator;
use IteratorAggregate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator as BasePaginator;
use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\JsonableInterface;

/**
 * @property BasePaginator $paginator The original paginator class
 */
class Paginator implements ArrayableInterface, ArrayAccess, Countable, IteratorAggregate, JsonableInterface {

    protected $query;
    protected $paginator;
    protected $presenter = true;

    public static function make(Builder $query, $perPage = null, array $columns = null) {
        return new static($query, $perPage, $columns);
    }

    public function __construct(Builder $query, $perPage = null, array $columns = null) {
        $this->setQuery($query, false);
        $this->init($perPage, $columns);
    }

    public function getPresenter() {
        return !!$this->presenter;
    }

    public function setPresenter($presenter) {
        $this->presenter = !!$presenter;
        return $this;
    }

    public function getQuery() {
        return $this->query;
    }

    /**
     * 
     * @return BasePaginator
     */
    public function getPaginator() {
        if (!$this->paginator) {
            throw new BadFunctionCallException();
        }
        return $this->paginator;
    }

    public function setQuery(Builder $query, $init = true) {
        $this->query = $query;
        if ($init) {
            $this->init();
        }
        return $this;
    }

    public function init($perPage = null, array $columns = null) {
        $this->paginator = $this->query->paginate($perPage, $columns);
        return $this;
    }

    public function __call($name, $arguments) {
        $result = call_user_func_array([$this->paginator, $name], $arguments);
        return $result && is_object($result) && $result instanceof Model && $result instanceof PresenterAwareInterface && $this->presenter ? $result->presenter : $result;
    }

    public function __get($name) {
        $result = $this->paginator->$name;
        return $result && is_object($result) && $result instanceof Model && $result instanceof PresenterAwareInterface && $this->presenter ? $result->presenter : $result;
    }

    public function __set($name, $value) {
        $this->paginator->$name = $value;
    }

    public function count() {
        return $this->paginator->count();
    }

    public function getIterator() {
        return $this->presenter ? new ArrayIterator($this->paginator->getItems()) : new BaseArrayIterator($this->paginator->getItems());
    }

    public function offsetExists($offset) {
        return $this->paginator->offsetExists($key);
    }

    public function offsetGet($offset) {
        return $this->__call('offsetGet', func_get_args());
    }

    public function offsetSet($offset, $value) {
        return $this->__call('offsetSet', func_get_args());
    }

    public function offsetUnset($offset) {
        return $this->__call('offsetUnset', func_get_args());
    }

    public function toArray() {
        return array(
            'total' => $this->paginator->total, 'per_page' => $this->paginator->perPage,
            'current_page' => $this->paginator->currentPage, 'last_page' => $this->paginator->lastPage,
            'from' => $this->paginator->from, 'to' => $this->paginator->to, 'data' => $this->getCollection()->toArray(),
        );
    }

    /**
     * Get a collection instance containing the items.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCollection() {
        return new Collection($this->paginator->getItems());
    }

    public function toJson($options = 0) {
        return json_encode($this->toArray(), $options);
    }

}
