<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

use Quanta\Container\Parameter;
use Quanta\Container\FactoryInterface;

final class ParsingFailure implements ParsedFactoryInterface
{
    /**
     * The value the parser failed to produce a factory from.
     *
     * @var mixed
     */
    private $value;

    /**
     * Constructor.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function success(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function factory(): FactoryInterface
    {
        return new Parameter($this->value);
    }
}
