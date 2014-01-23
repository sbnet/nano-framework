<?php
namespace NanoFramework\Utilities;

/**
* ArrayIterator
*
* @package NanoFramework\Utilities
* @author Stéphane BRUN
* @version 0.0.1
* @abstract
*/
abstract class ArrayIterator
{
    protected $data = array();
    private $position = 0;

    public function current()
    {
        return $this->data[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid()
    {
        return $this->offsetExists($this->position);
    }

    public function seek($position)
    {
        $this->position = $position;

        if(!$this->valid())
        {
            NanoFramework\Kernel\Exception(_("Invalid seek position : $position"));;
        }
    }

    public function count()
    {
        return count($this->data);
    }

    public function offsetExists($index)
    {
        return isset($this->data[$index]);
    }

    public function offsetGet($index)
    {
        return $this->data[$index];
    }

    public function offsetSet($index, $newval)
    {
        $this->data[$index] = $newval;
    }

    public function offsetUnset($index)
    {
        unset($this->data[$index]);
    }
}
