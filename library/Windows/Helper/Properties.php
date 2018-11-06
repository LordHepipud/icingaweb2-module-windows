<?php

namespace ipl\Orm;

trait Properties
{
    /** @var array */
    private $properties;

    public function getProperty($key)
    {
        $key = Str::camel($key);

        $mutator = "get{$key}Property";

        if (method_exists($this, $mutator)) {
            return $this->$mutator();
        }

        if (array_key_exists($key, $this->properties)) {
            return $this->properties[$key];
        }

        return null;
    }

    public function setProperty($key, $value)
    {
        $key = Str::camel($key);

        $mutator = "set{$key}Property";

        if (method_exists($this, $mutator)) {
            $this->$mutator($value);

            return $this;
        }

        $this->properties[$key] = $value;

        return $this;
    }

    public function setProperties(array $properties = [])
    {
        foreach ($properties as $key => $value) {
            $this->setProperty($key, $value);
        }

        return $this;
    }

    /**
     * Check whether an offset exists
     *
     * @param   mixed   $offset
     *
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return $this->getProperty($offset) !== null;
    }

    /**
     * Get the value for an offset
     *
     * @param   mixed   $offset
     *
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        return $this->getProperty($offset);
    }

    /**
     * Set the value for an offset
     *
     * @param   mixed   $offset
     * @param   mixed   $value
     */
    public function offsetSet($offset, $value)
    {
        $this->setProperty($offset, $value);
    }

    /**
     * Unset the value for an offset
     *
     * @param   mixed   $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->properties[$offset]);
    }

    /**
     * Get the value of a non-public property
     *
     * This is a PHP magic method which is implicitly called upon access to non-public properties,
     * e.g. `$value = $object->property;`.
     * Do not call this method directly.
     *
     * @param   mixed   $key
     *
     * @return  mixed
     */
    public function __get($key)
    {
        return $this->getProperty($key);
    }

    /**
     * Set the value of a non-public property
     *
     * This is a PHP magic method which is implicitly called upon access to non-public properties,
     * e.g. `$object->property = $value;`.
     * Do not call this method directly.
     *
     * @param   string  $key
     * @param   mixed   $value
     */
    public function __set($key, $value)
    {
        $this->setProperty($key, $value);
    }

    /**
     * Check whether a non-public property is defined and not null
     *
     * This is a PHP magic method which is implicitly called upon access to non-public properties,
     * e.g. `isset($object->property);`.
     * Do not call this method directly.
     *
     * @param   string  $key
     *
     * @return  bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Unset the value of a non-public property
     *
     * This is a PHP magic method which is implicitly called upon access to non-public properties,
     * e.g. `unset($object->property);`. This method does nothing if the property does not exist.
     * Do not call this method directly.
     *
     * @param   string  $key
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }
}
