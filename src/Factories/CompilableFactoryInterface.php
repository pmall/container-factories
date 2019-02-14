<?php declare(strict_types=1);

namespace Quanta\Container\Factories;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\Template;

interface CompilableFactoryInterface
{
    /**
     * The factory.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @return mixed
     */
    public function __invoke(ContainerInterface $container);

    /**
     * Return a string representation of the factory.
     *
     * A template is given to ease its creation.
     *
     * @param \Quanta\Container\Compilation\Template $template
     * @return string
     */
    public function compiled(Template $template): string;
}
