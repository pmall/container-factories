<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

final class ContainerEntry
{
    /**
     * The container variable name.
     *
     * @var string
     */
    private $container;

    /**
     * The container entry id.
     *
     * @var string $id
     */
    private $id;

    /**
     * Constructor.
     *
     * @param string $container
     * @param string $id
     */
    public function __construct(string $container, string $id)
    {
        $this->container = $container;
        $this->id = $id;
    }

    /**
     * Return a string representation of the container entry.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('$%s->get(\'%s\')', $this->container, $this->id);
    }
}
