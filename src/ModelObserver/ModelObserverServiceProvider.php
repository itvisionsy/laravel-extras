<?php

namespace ItvisionSy\LaravelExtras\ModelObserver;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use function app_path;

/**
 * Description of ModelObserverServiceProvider
 *
 * @author muhannad
 */
class ModelObserverServiceProvider extends ServiceProvider {

    public function register() {
        $this->app->events->subscribe($this);
    }

    public function subscribe(Dispatcher $events) {
        $events->listen('eloquent.*', function($model) use ($events) {
            $event = array_get(explode(".", array_get(explode(":", $events->firing()), 0)), 1);
            $file = app_path('observers/' . get_class($model) . 'Observer.php');
            if (file_exists($file)) {
                $class = get_class($model) . "Observer";
                return (new $class())->fire($event, $model);
            }
        });
    }

}
