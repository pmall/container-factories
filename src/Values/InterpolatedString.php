<?php declare(strict_types=1);

namespace Quanta\Container\Values;

use Psr\Container\ContainerInterface;

use Quanta\Container\Utils;

final class InterpolatedString implements ValueInterface
{
    /**
     * The sprintf format of the string.
     *
     * @var string
     */
    private $format;

    /**
     * The identifiers of the container entries used as sprintf arguments.
     *
     * @var string[]
     */
    private $ids;

    /**
     * Constructor.
     *
     * @param string $format
     * @param string $id
     * @param string ...$ids
     */
    public function __construct(string $format, string $id, string ...$ids)
    {
        $this->format = $format;
        $this->ids = array_merge([$id], $ids);
    }

    /**
     * @inheritdoc
     */
    public function value(ContainerInterface $container)
    {
        return vsprintf($this->format, array_map([$container, 'get'], $this->ids));
    }

    /**
     * @inheritdoc
     */
    public function str(string $container): string
    {
        return Utils::selfExecutingClosureStr($container, ...[
            vsprintf('return vsprintf(\'%s\', array_map([$%s, \'get\'], %s));', [
                addslashes($this->format),
                $container,
                Utils::arrayStr(array_map([Utils::class, 'quoted'], $this->ids)),
            ]),
        ]);
    }
}
