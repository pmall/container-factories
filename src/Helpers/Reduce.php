<?php declare(strict_types=1);

namespace Quanta\Container\Helpers;

final class Reduce
{
    /**
     * The method to call on the reducer.
     *
     * @var string
     */
    private $method;

    /**
     * Constructor.
     *
     * @param string $method
     */
    public function __construct(string $method)
    {
        $this->method = $method;
    }

    /**
     * Call the method on the given object with the given carried value.
     *
     * @param mixed     $carried
     * @param object    $object
     * @return mixed
     */
    public function __invoke($carried, $object)
    {
        return $object->{$this->method}($carried);
    }
}
