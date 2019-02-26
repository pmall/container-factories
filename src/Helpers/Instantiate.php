<?php declare(strict_types=1);

namespace Quanta\Container\Helpers;

final class Instantiate
{
    /**
     * The class to instantiate.
     *
     * @var string
     */
    private $class;

    /**
     * Constructor.
     *
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * Return an instance of the class using the given arguments.
     *
     * @param mixed ...$xs
     * @return object
     */
    public function __invoke(...$xs)
    {
        return new $this->class(...$xs);
    }
}
