<?php declare(strict_types=1);

namespace Quanta\Container\Configuration\Passes\Tagging;

final class Entries
{
    /**
     * The array of ids to tag.
     *
     * @var string[]
     */
    private $ids;

    /**
     * Constructor.
     *
     * @param string ...$ids
     */
    public function __construct(string ...$ids)
    {
        $this->ids = $ids;
    }

    /**
     * Return whether the given id is in the list.
     *
     * @param string $id
     * @return bool
     */
    public function __invoke(string $id): bool
    {
        return in_array($id, $this->ids);
    }
}
