<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

use Quanta\Container\FactoryArray;
use Quanta\Container\FactoryInterface;

final class ParsedFactoryArray implements ParsingResultInterface
{
    /**
     * The array of parsed factories.
     *
     * @var \Quanta\Container\Parsing\ParsingResultInterface[]
     */
    private $parsed;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Parsing\ParsingResultInterface[] $parsed
     * @throws \InvalidArgumentException
     */
    public function __construct(array $parsed)
    {
        $result = \Quanta\ArrayTypeCheck::result($parsed, ParsingResultInterface::class);

        if (! $result->isValid()) {
            throw new \InvalidArgumentException(
                $result->message()->constructor($this, 1)
            );
        }

        $this->parsed = $parsed;
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
        return new FactoryArray(array_map(function ($parsed) {
            return $parsed->factory();
        }, $this->parsed));
    }
}
