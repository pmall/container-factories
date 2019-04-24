<?php declare(strict_types=1);

namespace Quanta\Container\Configuration\Tagging;

final class CompositeTagging
{
    /**
     * The predicates.
     *
     * @var callable[]
     */
    private $predicates;

    /**
     * Constructor.
     *
     * @param callable ...$predicates
     */
    public function __construct(callable ...$predicates)
    {
        $this->predicates = $predicates;
    }

    /**
     * Return whether the given id satisfy at least one predicate.
     *
     * @param string $id
     * @return bool
     */
    public function __invoke(string $id): bool
    {
        foreach ($this->predicates as $predicate) {
            if ($predicate($id)) {
                return true;
            }
        }

        return false;
    }
}
