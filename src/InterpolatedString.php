<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

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
    public function compiled(string $container, callable $compiler): string
    {
        if (count($this->ids) == 0) {
            return (string) new Formatting\Quoted($this->format);
        }

        return vsprintf('vsprintf(%s, %s)', [
            new Formatting\Quoted($this->format),
            new Formatting\ContainerEntryArray($container, ...$this->ids),
        ]);
    }
}
