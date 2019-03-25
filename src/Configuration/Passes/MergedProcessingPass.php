<?php declare(strict_types=1);

namespace Quanta\Container\Configuration\Passes;

use Quanta\Container\Utils;

final class MergedProcessingPass implements ProcessingPassInterface
{
    /**
     * The processing passes to merge.
     *
     * @var \Quanta\Container\Configuration\Passes\ProcessingPassInterface[]
     */
    private $passes;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Configuration\Passes\ProcessingPassInterface ...$passes
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
            array_merge([], ...Utils::plucked($this->passes, 'aliases', $id))
        );
    }

    /**
     * @inheritdoc
     */
    public function tags(string ...$ids): array
    {
        return array_map('array_unique',
            array_merge_recursive([], ...Utils::plucked($this->passes, 'tags', ...$ids))
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
