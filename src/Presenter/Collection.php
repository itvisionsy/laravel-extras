<?php

namespace ItvisionSy\LaravelExtras\Presenter;

use ArrayIterator as Iterator;
use Illuminate\Database\Eloquent\Collection as ECollection;

/**
 * Description of PresenterCollection
 *
 * @author muhannad
 */
class Collection extends ECollection {

    protected $presenter = false;

    public function presenter($set = null) {
        if ($set === null) {
            return $this->presenter;
        } else {
            $this->presenter = !!$set;
        }
    }

    public function getIterator() {
        if ($this->presenter) {
            return new ArrayIterator($this->items);
        } else {
            return new Iterator($this->items);
        }
    }

}
