<?php declare(strict_types=1);

namespace Quanta\Container\Values;

use Psr\Container\ContainerInterface;

use Quanta\Container\Helpers\Pluck;
use Quanta\Container\Helpers\LinesStr;
use Quanta\Container\Helpers\IndentedStr;

final class Instance implements ValueInterface
{
    /**
     * The name of the class to instantiate.
     *
     * @var string
     */
    private $class;

    /**
     * The array of values used as class constructor arguments.
     *
     * @var \Quanta\Container\Values\ValueInterface[]
     */
    private $arguments;

    /**
     * Constructor.
     *
     * @param string                                    $class
     * @param \Quanta\Container\Values\ValueInterface   ...$arguments
     */
    public function __construct(string $class, ValueInterface ...$arguments)
    {
        $this->class = $class;
        $this->arguments = $arguments;
    }

    /**
     * @inheritdoc
     */
    public function value(ContainerInterface $container)
    {
        return new $this->class(...array_map(
            new Pluck('value', $container),
            $this->arguments
        ));
    }

    /**
     * @inheritdoc
     */
    public function str(string $container): string
    {
        if (count($this->arguments) > 0) {
            return vsprintf('new \%s(%s%s%s)', [
                $this->class,
                PHP_EOL,
                new IndentedStr((string) new LinesStr(...array_map(
                    new Pluck('str', $container),
                    $this->arguments
                ))),
                PHP_EOL,
            ]);
        }

        return sprintf('new \%s', $this->class);
    }
}
