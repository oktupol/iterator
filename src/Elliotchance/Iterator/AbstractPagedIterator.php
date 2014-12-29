<?php

namespace Elliotchance\Iterator;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use OutOfBoundsException;

abstract class AbstractPagedIterator implements Countable, ArrayAccess
{
    /**
     * @var array
     */
    protected $cachedPages = [];

    /**
     * @return integer
     */
    abstract public function getPageSize();

    /**
     * @return integer
     */
    abstract public function getTotalSize();

    /**
     * @param integer $pageNumber
     * @return array
     */
    abstract public function getPage($pageNumber);

    /**
     * @return integer
     */
    public function count()
    {
        return $this->getTotalSize();
    }

    /**
     * @param integer $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $offset >= 0 && $offset < $this->getTotalSize();
    }

    /**
     * @param integer $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (!is_int($offset)) {
            throw new InvalidArgumentException("Index must be a positive integer: $offset");
        }
        if (!$this->offsetExists($offset)) {
            throw new OutOfBoundsException("Index out of bounds: $offset");
        }

        $page = (int) ($offset / $this->getPageSize());
        if (!array_key_exists($page, $this->cachedPages)) {
            $this->cachedPages[$page] = $this->getPage($page);
        }
        return $this->cachedPages[$page][$offset % $this->getPageSize()];
    }

    public function offsetSet($offset, $value)
    {
    }

    public function offsetUnset($offset)
    {
    }
}
