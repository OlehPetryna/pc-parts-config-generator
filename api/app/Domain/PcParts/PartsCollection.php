<?php
declare(strict_types=1);

namespace App\Domain\PcParts;


class PartsCollection implements \Iterator
{
    /**@var PcPart[] $collection*/
    private $collection;

    private $iterator;

    public function __construct(array $parts)
    {
        $this->collection = $parts;
    }

    public static function fromIdsMap(array $map): self
    {
        $collection = [];
        $namespace = PcPart::ENTITIES_NAMESPACE . '\\';
        foreach ($map as $class => $id) {
            $className = "{$namespace}{$class}";
            $collection[] = $className::find($id);
        }

        return new self($collection);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->collection[$this->iterator];
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        return $this->collection[++$this->iterator];
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->iterator;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return isset($this->collection[$this->key()]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->iterator = 0;
    }

    public function asArray(): array
    {
        return $this->collection;
    }
}