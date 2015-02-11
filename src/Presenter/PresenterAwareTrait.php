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

    public function getPresenterAttribute() {
        if ($this->presenter === null) {
            $reflection = new ReflectionClass($this);
            $presenterClassName = $reflection->getNamespaceName() . "\\" . $reflection->getShortName() . "Presenter";
            $this->presenter = new $presenterClassName($this);
        }
        return $this->presenter;
    }

    public function newCollection(array $models = []) {
        $collectionClass = $this->collectionClassName();
        return new $collectionClass($models);
    }

}
