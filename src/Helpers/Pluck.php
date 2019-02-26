<?php declare(strict_types=1);

namespace Quanta\Container\Helpers;

final class Pluck
{
    /**
     * The method to call on the objects.
     *
     * @var string
     */
    private $method;

    /**
     * The arguments the method is called with.
     *
     * @var array
     */
    private $xs;

    /**
     * Constructor.
     *
     * @param string    $method
     * @param mixed     ...$xs
     */
    public function __construct(string $method, ...$xs)
    {
        $this->method = $method;
        $this->xs = $xs;
    }

    /**
     * Call the method on the given object with the arguments.
     *
     * @param object $object
     * @return mixed
     */
    public function __invoke($object)
    {
        return $object->{$this->method}(...$this->xs);
    }
}
