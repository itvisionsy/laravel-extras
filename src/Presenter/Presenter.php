<?php

namespace ItvisionSy\LaravelExtras\Presenter;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of Presenter
 *
 * @author muhannad
 * 
 * @property Model $model
 */
abstract class Presenter {

    protected $model;
    protected $useModelMethods = false;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function __get($name) {
        switch ($name) {
            case 'model':
                return $this->model;
                break;
            default:
                $uc2ccName = implode('', array_map(function($value) {
                            return ucfirst($value);
                        }, explode('_', $name)));
                $result = method_exists($this, "get" . $uc2ccName . 'Attribute') ? call_user_func([$this, "get" . $uc2ccName . 'Attribute']) : $this->model->$name;
                if ($result instanceof Model && $result instanceof PresenterAwareInterface) {
                    $result = $result->presenter;
                } elseif ($result instanceof Collection) {
                    $result->presenter(true);
                }
                return $result;
                break;
        }
    }

    public function __call($name, $arguments) {
        switch ($name) {
            default:
                if ($this->useModelMethods) {
                    return call_user_func_array([$this->model, $name], $arguments);
                } else {
                    user_error("Method `$name` was not found in " . get_class($this), E_USER_ERROR);
                }
        }
    }

    abstract public function __toString();

    public function getToStringAttribute() {
        return $this->__toString();
    }
    
    public function toString(){
        return $this->__toString();
    }

}
