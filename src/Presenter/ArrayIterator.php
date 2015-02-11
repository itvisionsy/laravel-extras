<?php

namespace ItvisionSy\LaravelExtras\Presenter;

use ArrayIterator as Iterator;
use Illuminate\Database\Eloquent\Model;

class ArrayIterator extends Iterator {

    public function current() {
        $result = parent::current();
        if ($result instanceof Model && $result instanceof PresenterAwareInterface) {
            $result = $result->presenter;
        }
        return $result;
    }

}
