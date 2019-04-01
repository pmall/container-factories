<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\Template;
use Quanta\Container\Compilation\IndentedString;
use Quanta\Container\Compilation\CompilableInterface;

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
     * Return a new EnvVar from the given name, default value and type.
     *
     * @param string $name
     * @param string $default
     * @param string $type
     * @return \Quanta\Container\EnvVar
     */
    public static function instance(string $name, string $default = '', string $type = 'string'): self
    {
        return new self($name, $default, $type);
    }

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
    public function compilable(string $container): CompilableInterface
    {
        $tpl = vsprintf('(function () {%s%s%s})()', [
            PHP_EOL,
            new IndentedString(implode(PHP_EOL, [
                '$value = getenv(%s);',
                'if ($value === false) $value = %s;',
                'settype($value, %s);',
                'return $value;',
            ])),
            PHP_EOL,
        ]);

        return new Template($tpl, $this->name, $this->default, $this->type);
    }
}
