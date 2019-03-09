<?php declare(strict_types=1);

namespace Quanta\Container\Passes;

final class MergedProcessingPass implements ProcessingPassInterface
{
    /**
     * The processing passes to merge.
     *
     * @var \Quanta\Container\Passes\ProcessingPassInterface[]
     */
    private $passes;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Passes\ProcessingPassInterface ...$passes
     */
    public function __construct(ProcessingPassInterface ...$passes)
    {
        $this->passes = $passes;
    }

    /**
     * @inheritdoc
     */
    public function processed(string ...$ids): array
    {
        $factories = [];

        foreach ($this->passes as $pass) {
            $factories = $pass->processed(...$ids);

            $ids = array_keys($factories);
        }

        return $factories;
    }
}
