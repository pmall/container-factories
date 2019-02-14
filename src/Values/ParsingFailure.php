<?php declare(strict_types=1);

namespace Quanta\Container\Values;

final class ParsingFailure implements ParsedValueInterface
{
    /**
     * The error message.
     *
     * @var string
     */
    private $message;

    /**
     * Constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        $this->message = $message;
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
    public function value(): ValueInterface
    {
        throw new \LogicException($this->message);
    }
}
