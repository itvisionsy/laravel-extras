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
            return $this;
        }
    }

    public function getIterator() {
        if ($this->presenter) {
            return new ArrayIterator($this->items);
        } else {
            return new Iterator($this->items);
        }
    }

    public function paginate(\Illuminate\Database\Eloquent\Builder $query, $size = null) {
        $paginator = $query->paginate($size);
        $this->paginator($paginator);
        return $paginator;
    }

    public function paginator(\Illuminate\Pagination\Paginator &$paginator) {
        $items = [];
        foreach ($this as $item) {
            $items[] = $item;
        }
        $paginator->setItems($items);
        return $paginator;
    }

}
