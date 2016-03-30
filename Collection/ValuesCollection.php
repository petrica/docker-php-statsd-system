<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/29/2016
 * Time: 1:49
 */
namespace Petrica\StatsdSystem\Collection;

use Countable, IteratorAggregate, ArrayAccess;

class ValuesCollection implements Countable, IteratorAggregate, ArrayAccess
{
    /**
     * @var array
     */
    private $values;

    /**
     * ValuesCollection constructor
     *
     * @param $values array
     */
    public function __construct(array $values = array())
    {
        $this->values = $values;
    }

    /**
     * Add a value to collection
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function add($key, $value)
    {
        $this->values[$key] = $value;

        return $this;
    }

    /**
     * Return stored values
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->values);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->values[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->values[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->values);
    }
}