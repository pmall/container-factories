<?php declare(strict_types=1);

namespace Quanta\Container\Factories;

use Psr\Container\ContainerInterface;

use Quanta\Container\Helpers\IndentedStr;

final class CompiledFactory
{
    /**
     * The container variable name.
     *
     * @var string
     */
    private $container;

    /**
     * The previous parameter declaration.
     *
     * @var string
     */
    private $previous;

    /**
     * The body of the factory.
     *
     * @var string
     */
    private $body;

    /**
     * Constructor.
     *
     * @param string $container
     * @param string $previous
     * @param string $body
     */
    public function __construct(string $container, string $previous, string $body)
    {
        $this->container = $container;
        $this->previous = $previous;
        $this->body = $body;
    }

    /**
     * Return a string representation of the factory.
     *
     * @return string
     */
    public function __toString()
    {
        return vsprintf('function (\%s $%s) {%s%s%s}', [
            ContainerInterface::class,
            implode(', ', array_filter([$this->container, $this->previous])),
            PHP_EOL,
            new IndentedStr($this->body),
            PHP_EOL,
        ]);
    }
}
