<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\IndentedString;

final class FactoryArray implements FactoryInterface
{
    /**
     * The array of factories.
     *
     * @var \Quanta\Container\FactoryInterface[]
     */
    private $factories;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\FactoryInterface[] $factories
     * @throws \InvalidArgumentException
     */
    public function __construct(array $factories)
    {
        $result = \Quanta\ArrayTypeCheck::result($factories, FactoryInterface::class);

        if (! $result->isValid()) {
            throw new \InvalidArgumentException(
                $result->message()->constructor($this, 1)
            );
        }

        $this->factories = $factories;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        return array_map(function ($factory) use ($container) {
            return $factory($container);
        }, $this->factories);
    }

    /**
     * @inheritdoc
     */
    public function compiled(string $container, callable $compiler): string
    {
        if (count($this->factories) == 0) {
            return '[]';
        }

        return implode(PHP_EOL, [
            '[',
            new IndentedString(implode(PHP_EOL, array_map(function ($key, $factory) use ($container, $compiler) {
                return vsprintf('%s => %s,', [
                    is_int($key) ? $key : '\'' . $key . '\'',
                    $factory->compiled($container, $compiler),
                ]);
            }, array_keys($this->factories), $this->factories))),
            ']',
        ]);
    }
}
