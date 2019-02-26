<?php declare(strict_types=1);

namespace Quanta\Container\Values;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\ArrayStr;
use Quanta\Container\Compilation\SelfExecutingClosure;

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
        $xs = array_map([$container, 'get'], $this->ids);

        return vsprintf($this->format, $xs);
    }

    /**
     * @inheritdoc
     */
    public function str(string $container): string
    {
        $tpl = 'return vsprintf(%s, array_map([$%s, \'get\'], %s));';

        return (string) new SelfExecutingClosure($container, vsprintf($tpl, [
            $this->quoted($this->format),
            $container,
            new ArrayStr(array_map([$this, 'quoted'], $this->ids)),
        ]));
    }

    /**
     * Return the given string with quotes.
     *
     * @param string $str
     * @return string
     */
    private function quoted(string $str): string
    {
        return sprintf('\'%s\'', $str);
    }
}