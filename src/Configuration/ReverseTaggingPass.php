<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\Factories\Tag;

use function Quanta\Exceptions\areAllTypedAs;
use Quanta\Exceptions\ArrayArgumentTypeErrorMessage;

final class ReverseTaggingPass implements ConfigurationPassInterface
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
            $factories[$id] = new Tag(...array_filter($ids, $predicate));
        }

        return $factories;
    }
}
