<?php declare(strict_types=1);

namespace Quanta\Container\Maps;

use Quanta\Container\Utils;

final class MergedFactoryMap implements FactoryMapInterface
{
    /**
     * The factory maps.
     *
     * @var \Quanta\Container\Maps\FactoryMapInterface[]
     */
    private $maps;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Maps\FactoryMapInterface ...$maps
     */
    public function __construct(FactoryMapInterface ...$maps)
    {
        $this->maps = $maps;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        return array_merge([], ...Utils::plucked($this->maps, 'factories'));
    }
}
