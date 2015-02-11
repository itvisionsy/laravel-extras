<?php

namespace ItvisionSy\LaravelExtras\ModelObserver;

abstract class ModelObserver {

    public function fire($event, $object) {
        return $this->$event($object);
    }

    public function __call($name, $arguments) {
        switch ($name) {
            case 'creating':
            case 'created':
            case 'updating':
            case 'updated':
            case 'saving':
            case 'saved':
            case 'deleting':
            case 'deleted':
            case 'restoring':
            case 'restored':
            default:
                return true;
                break;
        }
    }

}
