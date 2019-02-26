<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

use Psr\Container\ContainerInterface;

final class SelfExecutingClosureStr
{
    /**
     * The container variable name.
     *
     * @var string
     */
    private $container;

    /**
     * The closure body.
     *
     * @var string
     */
    private $body;

    /**
     * Constructor.
     *
     * @param string $container
     * @param string $body
     */
    public function __construct(string $container, string $body)
    {
        $this->container = $container;
        $this->body = $body;
    }

    /**
     * Return a string representation of the self contained closure.
     *
     * @return string
     */
    public function __toString()
    {
        $tpl = '(function (\%s $%s) {%s%s%s})($%s)';

        return vsprintf($tpl, [
            ContainerInterface::class,
            $this->container,
            PHP_EOL,
            new IndentedStr($this->body),
            PHP_EOL,
            $this->container,
        ]);
    }
}
