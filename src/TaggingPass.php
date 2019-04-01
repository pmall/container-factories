<?php declare(strict_types=1);

namespace Quanta\Container;

final class TaggingPass implements ProcessingPassInterface
{
    /**
     * The id of the tag.
     *
     * @var string
     */
    private $id;

    /**
     * The predicate used to match container entry id to tag.
     *
     * @var callable
     */
    private $predicate;

    /**
     * Return a new TaggingPass from the given id and predicate.
     *
     * @param string    $id
     * @param callable  $predicate
     * @return \Quanta\Container\TaggingPass
     */
    public static function instance(string $id, callable $predicate): self
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
    public function aliases(string $id): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function tags(string ...$ids): array
    {
        return [$this->id => array_values(array_filter($ids, $this->predicate))];
    }

    /**
     * @inheritdoc
     */
    public function processed(string $id, callable $factory): callable
    {
        return $factory;
    }
}
