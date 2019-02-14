<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

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
     * Return all metadata.
     *
     * @return array[]
     */
    public function all(): array
    {
        $ids = array_map('array_keys', $this->maps);
        $ids = array_merge([], ...$ids);
        $ids = array_unique($ids);

        $metadata = array_map([$this, 'for'], $ids);

        return (array) array_combine($ids, $metadata);
    }

    /**
     * Return the metadata for the given id.
     *
     * @param string $id
     * @return array
     */
    public function for($id): array
    {
        $reducer = $this->reducer($id);

        return array_reduce($this->maps, $reducer, []);
    }

    /**
     * Return an array of id with metadata matching the given array.
     *
     * @param array $target
     * @return array[]
     */
    public function matching(array $target): array
    {
        $all = $this->all();
        $matcher = $this->matcher($target);

        return array_filter($all, $matcher);
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

    /**
     * Return a callable retruning whether an array of metatada is matching the
     * given target array.
     *
     * @param array $target
     * @return callable
     */
    private function matcher(array $target): callable
    {
        return function (array $source) use ($target): bool {
            return array_intersect_key($source, $target) == $target;
        };
    }
}
