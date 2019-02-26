<?php declare(strict_types=1);

namespace Quanta\Container\Values;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\SelfExecutingClosureStr;

final class EnvVar implements ValueInterface
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
    public function value(ContainerInterface $container)
    {
        $value = getenv($this->name);

        if ($value === false) $value = $this->default;

        settype($value, $this->type);

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function str(string $container): string
    {
        $tpl = implode(PHP_EOL, [
            '$value = getenv(\'%s\');',
            'if ($value === false) $value = \'%s\';',
            'settype($value, \'%s\');',
            'return $value;',
        ]);

        return (string) new SelfExecutingClosureStr($container, vsprintf($tpl, [
            $this->name,
            $this->default,
            $this->type,
        ]));
    }
}
