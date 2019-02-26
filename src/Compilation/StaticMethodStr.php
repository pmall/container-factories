<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

final class StaticMethodStr
{
    /**
     * The class name.
     *
     * @var string
     */
    private $class;

    /**
     * The static method.
     *
     * @var string
     */
    private $method;

    /**
     * Constructor.
     *
     * @param string $class
     * @param string $method
     */
    public function __construct(string $class, string $method)
    {
        $this->class = $class;
        $this->method = $method;
    }

    /**
     * Return the string representation of the static method callable.
     *
     * @return string
     */
    public function __toString()
    {
        return vsprintf('[\%s::class, \'%s\']', [
            ltrim($this->class, '\\'),
            $this->method
        ]);
    }
}
