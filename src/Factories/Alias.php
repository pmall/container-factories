<?php declare(strict_types=1);

namespace Quanta\Container\Factories;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\Template;

final class Alias implements CompilableFactoryInterface
{
    /**
     * The id of the container entry to alias.
     *
     * @var string
     */
    private $id;

    /**
     * Constructor.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        return $container->get($this->id);
    }

    /**
     * @inheritdoc
     */
    public function compiled(Template $template): string
    {
        $container = $template->containerVariableName();

        return $template->strWithReturnf('$%s->get(\'%s\')', ...[
            $container,
            $this->id,
        ]);
    }
}
