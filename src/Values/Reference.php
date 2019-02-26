<?php declare(strict_types=1);

namespace Quanta\Container\Values;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use Quanta\Container\Compilation\IndentedStr;
use Quanta\Container\Compilation\SelfExecutingClosureStr;

final class Reference implements ValueInterface
{
    /**
     * The container entry identifier.
     *
     * @var string
     */
    private $id;

    /**
     * Whether the reference is nullable.
     *
     * @var bool
     */
    private $nullable;

    /**
     * Constructor.
     *
     * @param string    $id
     * @param bool      $nullable
     */
    public function __construct(string $id, bool $nullable)
    {
        $this->id = $id;
        $this->nullable = $nullable;
    }

    /**
     * @inheritdoc
     */
    public function value(ContainerInterface $container)
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
    public function str(string $container): string
    {
        if ($this->nullable) {
            return (string) new SelfExecutingClosureStr($container, ...[
                vsprintf('if ($%s->has(\'%s\')) {%s%s%s%s%s}%sreturn null;', [
                    $container,
                    addslashes($this->id),
                    PHP_EOL,
                    new IndentedStr(vsprintf('try { return $%s->get(\'%s\'); }', [
                        $container,
                        addslashes($this->id),
                    ])),
                    PHP_EOL,
                    new IndentedStr(vsprintf('catch (\%s $e) { return null; }', [
                        NotFoundExceptionInterface::class,
                    ])),
                    PHP_EOL,
                    PHP_EOL,
                ])
            ]);
        }

        return sprintf('$%s->get(\'%s\')', $container, addslashes($this->id));
    }
}
