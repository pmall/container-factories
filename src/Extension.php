<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\Template;
use Quanta\Container\Compilation\CompilableFactory;
use Quanta\Container\Compilation\CompilableInterface;

final class Extension implements FactoryInterface
{
    /**
     * The factory to extend.
     *
     * @var callable
     */
    private $factory;

    /**
     * The extension.
     *
     * @var callable
     */
    private $extension;

    /**
     * Return a new Extension from the given factory and extension.
     *
     * @param callable $factory
     * @param callable $extension
     * @return \Quanta\Container\Extension
     */
    public static function instance(callable $factory, callable $extension): self
    {
        return new self($factory, $extension);
    }

    /**
     * Constructor.
     *
     * @param callable $factory
     * @param callable $extension
     */
    public function __construct(callable $factory, callable $extension)
    {
        $this->factory = $factory;
        $this->extension = $extension;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        return ($this->extension)($container, ($this->factory)($container));
    }

    /**
     * @inheritdoc
     */
    public function compilable(string $container): CompilableInterface
    {
        $tpl = sprintf('(%%s)($%s, (%%s)($%s))', $container, $container);

        return new Template($tpl, ...[
            new CompilableFactory($this->extension),
            new CompilableFactory($this->factory),
        ]);
    }
}
