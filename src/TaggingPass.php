<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Extension;
use Quanta\Container\Factories\EmptyArrayFactory;

use Quanta\Container\Helpers\Instantiate;

final class TaggingPass implements ProcessingPassInterface
{
    /**
     * The id of the tag.
     *
     * @var string
     */
    private $id;

    /**
     * The predicate used to match container entry ids to tag.
     *
     * @var callable
     */
    private $predicate;

    /**
     * Constructor.
     *
     * @param string    $id
     * @param callable  $predicate
     */
    public function __construct(string $id, callable $predicate)
    {
        $this->id = $id;
        $this->predicate = $predicate;
    }

    /**
     * @inheritdoc
     */
    public function processed(array $factories): array
    {
        $ids = array_filter(array_keys($factories), [$this, 'tagged']);

        $tags = array_map(new Instantiate(Tag::class), $ids);

        $factories[$this->id] = array_reduce($tags, ...[
            new Instantiate(Extension::class),
            $factories[$this->id] ?? new EmptyArrayFactory,
        ]);

        return $factories;
    }

    /**
     * Ensure the tag can't tag itself.
     *
     * @param string $id
     * @return bool
     */
    private function tagged(string $id): bool
    {
        return $id != $this->id && ($this->predicate)($id);
    }
}
