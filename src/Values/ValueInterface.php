<?php declare(strict_types=1);

namespace Quanta\Container\Values;

use Psr\Container\ContainerInterface;

interface ValueInterface
{
    /**
     * Return the value.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @return mixed
     */
    public function value(ContainerInterface $container);

    /**
     * Return a string representation of the value.
     *
     * @param string $container
     * @return string
     */
    public function str(string $container): string;
}
