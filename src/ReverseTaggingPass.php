<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Factories\Tag;

use function Quanta\Exceptions\areAllTypedAs;
use Quanta\Exceptions\ArrayArgumentTypeErrorMessage;

final class ReverseTaggingPass implements ConfigurationPassInterface
{
    /**
     * The tags to add to the associative array of factories.
     *
     * @var callable[]
     */
    private $tags;

    /**
     * Constructor.
     *
     * @var callable[] $tags
     */
    public function __construct(array $tags)
    {
        if (! areAllTypedAs('callable', $tags)) {
            throw new \InvalidArgumentException(
                (string) new ArrayArgumentTypeErrorMessage(1, 'callable', $tags)
            );
        }

        $this->tags = $tags;
    }

    /**
     * @inheritdoc
     */
    public function processed(array $factories): array
    {
        $ids = array_keys($factories);

        foreach ($this->tags as $id => $predicate) {
            $factories[$id] = new Tag(...array_filter($ids, $predicate));
        }

        return $factories;
    }
}
