<?php

namespace ItvisionSy\LaravelExtras\Presenter;

/**
 * Description of RichPresenter
 *
 * @author muhannad
 * 
 * @property Model $model
 */
abstract class RichPresenter extends Presenter {

    protected $useModelMethods = true;

    /**
     *
     * @var string[] a list of key=>['pattern','format'] for which the format will be applied for the attribute 
     */
    protected $formats = [
//        'phone' => ['/^(971)(\d)(\d{3})(\d+)$/', "+$1 (0)$2 $3 $4"],
//        'mobile' => ['/^(971)(5\d)(\d+)$/', "+$1 (0)$2 $3"],
    ];

    /**
     * The URL to be used to access the model's page. Preferred to be overriden for every presenter
     * 
     * @return string
     */
    protected function showUrl() {
        return route(strtolower(get_class($this->model)) . "s.show", [$this->id]);
    }

    /**
     * Allow the show URL method as an attribute
     * 
     * @return string
     */
    public function getShowUrlAttribute() {
        return $this->showUrl();
    }

    /**
     * The URL to be used to access the model index page. Preferred to be overriden for every presenter
     * 
     * @return string
     */
    protected function indexUrl() {
        return route(strtolower(get_class($this->model)) . "s.index");
    }

    /**
     * Allow the index URL method as an attribute
     * 
     * @return string
     */
    public function getIndexUrlAttribute() {
        return $this->indexUrl();
    }

    /**
     * Public invoker
     * 
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {
        if (substr($name, 0, 6) == 'linked') {
            return $this->_linked(substr($name, 6), $arguments);
        }
        if (substr($name, 0, 9) == 'formatted') {
            return $this->_formatted(substr($name, 9), $arguments);
        }
        return parent::__call($name, $arguments);
    }

    /**
     * Getter
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name) {

        //if linked
        if (substr($name, 0, 6) == 'linked') {
            return $this->_linked(substr($name, 6));
        }

        //else if formatted
        if (substr($name, 0, 9) == 'formatted') {
            return $this->_formatted(substr($name, 9));
        }

        //else get from parent as attribute
        $result = parent::__get($name);

        //if not in the parent as an attribute
        if ($result === null) {

            //check if it is a Ucfirst call for a property
            $nName = preg_replace_callback('/^([A-Z])(\S+)$/', function($matches) {
                return strtolower($matches[1]) . $matches[2];
            }, $name);

            //if so, get it
            if ($name !== $nName) {
                $result = ucfirst($this->$nName);
            }
        }

        //if not a Ucfirst call, 
        if ($result === null) {
            //check if it is an UPPERCASE call
            $nName = preg_replace_callback('/^([A-Z]+)$/', function($matches) {
                return strtolower($matches[1]);
            }, $name);

            //if so, get it
            if ($name !== $nName) {
                $result = strtoupper($this->$nName);
            }
        }

        //return the result whatever it is
        return $result;
    }

    /**
     * Format a property
     * 
     * @param string $name
     * @param array $options options to be passed to the formatter
     * @return string
     */
    protected function _formatted($name, array $options = []) {

        //check if formattedAttribute method exists for the property, call it,
        if (method_exists($this, "getFormatted{$name}Attribute")) {
            $text = call_user_func_array([$this, "getFormatted{$name}Attribute"], $options);
        } else {

            //else if get the attribute from the public getter
            $text = $this->__get($name);

            //get the lCFirst name
            $lname = strtolower(substr($name, 0, 1)) . substr($name, 1);

            //if format exists, get and format it.
            if (array_key_exists($lname, $this->formats)) {
                return preg_replace($this->formats[$lname][0], $this->formats[$lname][1], $text);
            }
        }

        //return $text
        return $text;
    }

    /**
     * Create a link titled with the property
     * 
     * @param string $name
     * @param array $options
     * @return string
     */
    protected function _linked($name, array $options = []) {
        //if linkedAttribute method exists, call it
        if (method_exists($this, "getLinked{$name}Attribute")) {
            return call_user_func_array([$this, "getLinked{$name}Attribute"], $options);
        } else {
            //else get the value from public getter
            $text = $this->__get($name);
            return "<a href='{$this->showUrl()}'>{$text}</a>";
        }
    }

    /**
     * A shorthand for checking and returning a specific value
     * 
     * @param mixed $valueToCheck
     * @param mixed $checkAgainst
     * @param mixed $ifValue a value to return when $valueToCheck is matched
     * @param mixed $elseValue a value to return when not matched
     * @param string $elseValueType value, method, or property, defines where to get the else value from
     * @param array $elseValueMethodArgs arguments for the elseValue if it is a method
     * @return mixed
     */
    public function ifEmptyElse($valueToCheck, $checkAgainst, $ifValue, $elseValue, $elseValueType = 'value', array $elseValueMethodArgs = []) {
        if ($valueToCheck === $checkAgainst) {
            return $ifValue;
        } else {
            switch ($elseValueType) {
                case 'value':
                    return $elseValue;
                case 'method':
                    return call_user_func_array([$this, $elseValue], $elseValueMethodArgs);
                case 'property':
                    return $this->$elseValue;
            }
        }
    }

}
