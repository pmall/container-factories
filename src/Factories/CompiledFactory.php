<?php declare(strict_types=1);

namespace Quanta\Container\Factories;

use Psr\Container\ContainerInterface;

use Quanta\Container\Utils;

final class CompiledFactory
{
    /**
     * The container variable name.
     *
     * @var string
     */
    private $container;

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
     * @param string $body
     */
    public function __construct(string $container, string $body)
    {
        $this->container = $container;
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
            $this->container,
            PHP_EOL,
            Utils::indented($this->body),
            PHP_EOL,
        ]);
    }
}
