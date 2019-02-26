<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Extension;
use Quanta\Container\Factories\EmptyArrayFactory;

use function Quanta\Exceptions\areAllTypedAs;
use Quanta\Exceptions\ArrayArgumentTypeErrorMessage;

final class ReverseTaggingPass implements ProcessingPassInterface
{
    /**
     * The associative array of predicates used to add tags to the factories.
     *
     * @var callable[]
     */
    private $predicates;

    /**
     * Constructor.
     *
     * @var callable[] $predicates
     */
    public function __construct(array $predicates)
    {
        if (! areAllTypedAs('callable', $predicates)) {
            throw new \InvalidArgumentException(
                (string) new ArrayArgumentTypeErrorMessage(1, 'callable', $predicates)
            );
        }

        $this->predicates = $predicates;
    }

    /**
     * @inheritdoc
     */
    public function processed(array $factories): array
    {
        $ids = array_keys($factories);

        foreach ($this->predicates as $id => $predicate) {
            $factories[$id] = $this->tag(...array_filter($ids, $predicate));
        }

        return $factories;
    }

    /**
     * Reduce the given container entry ids to a single tag.
     *
     * @param string ...$ids
     * @return callable
     */
    private function tag(string ...$ids): callable
    {
        return array_reduce($ids, [$this, 'reduced'], new EmptyArrayFactory);
    }

    /**
     * Add the given container entry id to the given tag.
     *
     * @param callable  $tag
     * @param string    $id
     * @return \Quanta\Container\Factories\Extension
     */
    private function reduced(callable $tag, string $id): Extension
    {
        return new Extension($tag, new Tag($id));
    }
}
