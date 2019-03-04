<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Extension;
use Quanta\Container\Factories\EmptyArrayFactory;

use Quanta\Container\Helpers\Instantiate;

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
        $result = \Quanta\ArrayTypeCheck::result($predicates, 'callable');

        if (! $result->isValid()) {
            throw new \InvalidArgumentException(
                $result->message()->constructor($this, 1)
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
