<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use Quanta\Container\Compilation\Template;
use Quanta\Container\Compilation\IndentedString;
use Quanta\Container\Compilation\ContainerEntry;
use Quanta\Container\Compilation\CompilableInterface;

final class Alias implements FactoryInterface
{
    /**
     * The container entry identifier.
     *
     * @var string
     */
    private $id;

    /**
     * Whether to return null when the container does not contains id.
     *
     * @var bool
     */
    private $nullable;

    /**
     * Return a new Alias from the given id and nullable.
     *
     * @param string    $id
     * @param bool      $nullable
     * @return \Quanta\Container\Alias
     */
    public static function instance(string $id, bool $nullable = false): self
    {
        return new self($id, $nullable);
    }

    /**
     * Constructor.
     *
     * @param string    $id
     * @param bool      $nullable
     */
    public function __construct(string $id, bool $nullable = false)
    {
        $this->id = $id;
        $this->nullable = $nullable;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        if ($this->nullable && ! $container->has($this->id)) {
            return null;
        }

        try {
            return $container->get($this->id);
        }

        catch (NotFoundExceptionInterface $e) {
            if ($this->nullable) {
                return null;
            }
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function compilable(string $container): CompilableInterface
    {
        if (! $this->nullable) {
            return new ContainerEntry($container, $this->id);
        }

        $tpl = vsprintf('(function ($container) {%s%s%s})($%s)', [
            PHP_EOL,
            new IndentedString(implode(PHP_EOL, [
                'if ($container->has(%s)) {',
                new IndentedString(implode(PHP_EOL, [
                    'try { return %s; }',
                    vsprintf('catch (%s $e) { return null; }', [
                        NotFoundExceptionInterface::class,
                    ]),
                ])),
                '}',
                'return null;',
            ])),
            PHP_EOL,
            $container,
        ]);

        return new Template($tpl, ...[
            $this->id,
            new ContainerEntry('container', $this->id),
        ]);
    }
}
