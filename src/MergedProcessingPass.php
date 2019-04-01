<?php declare(strict_types=1);

namespace Quanta\Container;

final class MergedProcessingPass implements ProcessingPassInterface
{
    /**
     * The processing passes to merge.
     *
     * @var \Quanta\Container\ProcessingPassInterface[]
     */
    private $passes;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ProcessingPassInterface ...$passes
     */
    public function __construct(ProcessingPassInterface ...$passes)
    {
        $this->passes = $passes;
    }

    /**
     * @inheritdoc
     */
    public function aliases(string $id): array
    {
        return array_unique(
            array_merge([], ...array_map(function ($pass) use ($id) {
                return $pass->aliases($id);
            }, $this->passes))
        );
    }

    /**
     * @inheritdoc
     */
    public function tags(string ...$ids): array
    {
        return array_map('array_unique',
            array_merge_recursive([], ...array_map(function ($pass) use ($ids) {
                return $pass->tags(...$ids);
            }, $this->passes))
        );
    }

    /**
     * @inheritdoc
     */
    public function processed(string $id, callable $factory): callable
    {
        foreach ($this->passes as $pass) {
            $factory = $pass->processed($id, $factory);
        }

        return $factory;
    }
}
