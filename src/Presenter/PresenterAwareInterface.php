<?php

namespace ItvisionSy\LaravelExtras\Presenter;

/**
 *
 * @author muhannad
 */
interface PresenterAwareInterface {

    public function getPresenterAttribute();

    public function newCollection(array $models = []);
}
