<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

use Quanta\Container\FactoryInterface;

final class ParsedFactory implements ParsingResultInterface
{
    /**
     * The parsed factory.
     *
     * @var \Quanta\Container\FactoryInterface
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @inheritdoc
     */
    public function isParsed(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function factory(): FactoryInterface
    {
        return $this->factory;
    }
}
