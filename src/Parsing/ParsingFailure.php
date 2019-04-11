<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

use Quanta\Container\FactoryInterface;

final class ParsingFailure implements ParsedFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function isParsed(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function factory(): FactoryInterface
    {
        throw new \LogicException('parsing failed');
    }
}
