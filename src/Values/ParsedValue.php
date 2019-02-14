<?php declare(strict_types=1);

namespace Quanta\Container\Values;

final class ParsedValue implements ParsedValueInterface
{
    /**
     * The result of the parsing.
     *
     * @var \Quanta\Container\Values\ValueInterface
     */
    private $value;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Values\ValueInterface $value
     */
    public function __construct(ValueInterface $value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function success(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function value(): ValueInterface
    {
        return $this->value;
    }
}
