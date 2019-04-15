<?php declare(strict_types=1);

namespace Quanta\Container\Formatting;

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
     * Whether to return null when the container does not contains id.
     *
     * @var bool
     */
    private $nullable;

    /**
     * Constructor.
     *
     * @param string    $container
     * @param string    $id
     * @param bool      $nullable
     */
    public function __construct(string $container, string $id, bool $nullable = false)
    {
        $this->container = $container;
        $this->id = $id;
        $this->nullable = $nullable;
    }

    /**
     * Return a string representation of the container entry.
     *
     * @return string
     */
    public function __toString()
    {
        $entry = sprintf('$%s->get(%s)', $this->container, new Quoted($this->id));

        if (! $this->nullable) {
            return $entry;
        }

        return implode(PHP_EOL, [
            sprintf('$%s->has(%s)', $this->container, new Quoted($this->id)),
            new IndentedString('? ' . $entry),
            new IndentedString(': null'),
        ]);
    }
}
