<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Tagging;
use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Extension;
use Quanta\Container\Factories\EmptyArrayFactory;

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
     * Return a new TaggingPass with the given id and predicate.
     *
     * @param string    $id
     * @param callable  $predicate
     * @return \Quanta\Container\TaggingPass
     */
    public static function instance(string $id, callable $predicate): TaggingPass
    {
        return new self($id, $predicate);
    }

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

        $tags = array_map([Tag::class, 'instance'], $ids);

        $factories[$this->id] = array_reduce($tags, ...[
            [Extension::class, 'instance'],
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
