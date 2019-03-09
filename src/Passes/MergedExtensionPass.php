<?php declare(strict_types=1);

namespace Quanta\Container\Passes;

final class MergedExtensionPass implements ExtensionPassInterface
{
    /**
     * The extension passes to merge.
     *
     * @var \Quanta\Container\Passes\ExtensionPassInterface[]
     */
    private $passes;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Passes\ExtensionPassInterface ...$passes
     */
    public function __construct(ExtensionPassInterface ...$passes)
    {
        $this->passes = $passes;
    }

    /**
     * @inheritdoc
     */
    public function extended(string $id, callable $factory): callable
    {
        foreach ($this->passes as $pass) {
            $factory = $pass->extended($id, $factory);
        }

        return $factory;
    }
}
