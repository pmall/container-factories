<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\Template;
use Quanta\Container\Compilation\Compilable;
use Quanta\Container\Compilation\ContainerEntry;
use Quanta\Container\Compilation\CompilableInterface;

final class InterpolatedString implements FactoryInterface
{
    /**
     * The sprintf format of the string.
     *
     * @var string
     */
    private $format;

    /**
     * The ids of the container entries used as sprintf arguments.
     *
     * @var string[]
     */
    private $ids;

    /**
     * Return a new InterpolatedString from the given sprintf format and
     * container entry ids.
     *
     * @param string $format
     * @param string ...$ids
     * @return \Quanta\Container\InterpolatedString
     */
    public static function instance(string $format, string ...$ids): self
    {
        return new self($format, ...$ids);
    }

    /**
     * Constructor.
     *
     * @param string $format
     * @param string ...$ids
     */
    public function __construct(string $format, string ...$ids)
    {
        $this->format = $format;
        $this->ids = $ids;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        return vsprintf($this->format, array_map([$container, 'get'], $this->ids));
    }

    /**
     * @inheritdoc
     */
    public function compilable(string $container): CompilableInterface
    {
        if (count($this->ids) == 0) {
            return new Compilable($this->format);
        }

        return new Template('vsprintf(%s, %s)', $this->format, array_map(function ($id) use ($container) {
            return new ContainerEntry($container, $id);
        }, $this->ids));
    }
}
