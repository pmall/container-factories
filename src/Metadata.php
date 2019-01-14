<?php declare(strict_types=1);

namespace Quanta\Container;

final class Metadata
{
    /**
     * The metadata maps.
     *
     * @var array[]
     */
    private $maps;

    /**
     * Constructor.
     *
     * @param array ...$maps
     */
    public function __construct(array ...$maps)
    {
        $this->maps = $maps;
    }

    /**
     * Return the metadata for the given id.
     *
     * @param string $id
     * @return array
     */
    public function for($id): array
    {
        return array_reduce($this->maps, $this->reducer($id), []);
    }

    /**
     * Return a callable reducing metadata for the given id.
     *
     * @param string $id
     * @return callable
     */
    private function reducer(string $id): callable
    {
        return function (array $carry, array $map) use ($id) {
            return array_merge($carry, $map[$id] ?? []);
        };
    }
}
