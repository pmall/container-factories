<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

final class ContainerEntryCollection
{
    /**
     * The container variable name.
     *
     * @var string
     */
    private $container;

    /**
     * The container entry ids.
     *
     * @var string[] $ids
     */
    private $ids;

    /**
     * Constructor.
     *
     * @param string $container
     * @param string ...$ids
     */
    public function __construct(string $container, string ...$ids)
    {
        $this->container = $container;
        $this->ids = $ids;
    }

    /**
     * Return a string representation of the container entry.
     *
     * @return string
     */
    public function __toString()
    {
        if (count($this->ids) == 0) {
            return '[]';
        }

        return implode(PHP_EOL, [
            '[',
            new IndentedString(implode(PHP_EOL, array_map(function ($id) {
                return (string) new ContainerEntry($this->container, $id) . ',';
            }, $this->ids))),
            ']',
        ]);
    }
}
