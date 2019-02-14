<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

final class InstanceStr
{
    /**
     * The class name.
     *
     * @var string
     */
    private $class;

    /**
     * The arguments the class constructor is invoked with.
     *
     * @var string[]
     */
    private $xs;

    /**
     * Constructor.
     *
     * @param string $class
     * @param string ...$xs
     */
    public function __construct(string $class, string ...$xs)
    {
        $this->class = $class;
        $this->xs = $xs;
    }

    /**
     * Return a string representation of the array.
     *
     * @return string
     */
    public function __toString()
    {
        if (count($this->xs) > 0) {
            return vsprintf('new \%s(%s%s%s)', [
                $this->class,
                PHP_EOL,
                new IndentedStr(implode(',' . PHP_EOL, $this->xs)),
                PHP_EOL,
            ]);
        }

        return sprintf('new \%s', $this->class);
    }
}
