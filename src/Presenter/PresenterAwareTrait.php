<?php

namespace ItvisionSy\LaravelExtras\Presenter;

use ReflectionClass;

/**
 *
 * @author muhannad
 */
trait PresenterAwareTrait {

    protected $presenter;

    protected function collectionClassName() {
        return '\\ItvisionSy\\LaravelExtras\\Presenter\\Collection';
    }

    protected function presenterClassName() {
        $reflection = new ReflectionClass($this);
        return $reflection->getNamespaceName() . "\\" . $reflection->getShortName() . "Presenter";
    }

    public function getPresenterAttribute() {
        if ($this->presenter === null) {
            $presenterClassName = $this->presenterClassName();
            $this->presenter = new $presenterClassName($this);
        }
        return $this->presenter;
    }

    public function newCollection(array $models = []) {
        $collectionClass = $this->_presenterCollectionClassName();
        return new $collectionClass($models);
    }

}
