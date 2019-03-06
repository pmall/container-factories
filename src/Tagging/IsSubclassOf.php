<?php declare(strict_types=1);

namespace Quanta\Container\Tagging;

final class IsSubclassOf
{
    /**
     * Ids must be the name of a subclass of theis interface/class to be tagged.
     *
     * @var string
     */
    private $class;

    /**
     * Constructor.
     *
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * Return whether the given id is the name of a subclass of the class.
     *
     * @param string $id
     * @return bool
     */
    public function __invoke(string $id): bool
    {
        return is_subclass_of($id, $this->class, true);
    }
}
