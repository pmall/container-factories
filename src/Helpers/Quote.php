<?php declare(strict_types=1);

namespace Quanta\Container\Helpers;

final class Quote
{
    /**
     * Add quotes to the given string.
     *
     * @param string $value
     * @return string
     */
    public function __invoke(string $value)
    {
        return sprintf('\'%s\'', addslashes($value));
    }
}
