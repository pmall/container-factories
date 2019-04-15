<?php declare(strict_types=1);

namespace Quanta\Container\Formatting;

final class ContainerEntryArray
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
        return (string) new StringArray(array_map(function ($id) {
            return new ContainerEntry($this->container, $id);
        }, $this->ids));
    }
}
