<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Extension;
use Quanta\Container\Factories\EmptyArrayFactory;

use Quanta\Container\Helpers\Instantiate;

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
            $tags = array_map(new Instantiate(Tag::class), ...[
                array_filter($ids, $predicate),
            ]);

            $factories[$id] = array_reduce($tags, ...[
                new Instantiate(Extension::class),
                new EmptyArrayFactory,
            ]);
        }

        return $factories;
    }
}
