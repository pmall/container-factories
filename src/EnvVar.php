<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

final class EnvVar implements FactoryInterface
{
    /**
     * The name of the env variable.
     *
     * @var string
     */
    private $name;

    /**
     * The default value to return when the env variable is not set.
     *
     * Default to ''.
     *
     * @var string
     */
    private $default;

    /**
     * The type we want the value of the env variable to be casted to.
     *
     * It is used as the second parameter of the settype() method.
     *
     * Default to 'string'.
     *
     * @var string $type
     */
    private $type;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $default
     * @param string $type
     */
    public function __construct(string $name, string $default = '', string $type = 'string')
    {
        $this->name = $name;
        $this->default = $default;
        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        $value = getenv($this->name);

        if ($value === false) $value = $this->default;

        settype($value, $this->type);

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function compiled(string $container, callable $compiler): string
    {
        return implode(PHP_EOL, [
            '(function () {',
            new Formatting\IndentedString(implode(PHP_EOL, [
                sprintf('$value = getenv(\'%s\');', $this->name),
                sprintf('if ($value === false) $value = \'%s\';', $this->default),
                sprintf('settype($value, \'%s\');', $this->type),
                'return $value;',
            ])),
            '})()',
        ]);
    }
}
