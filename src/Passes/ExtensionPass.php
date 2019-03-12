<?php declare(strict_types=1);

namespace Quanta\Container\Passes;

use Quanta\Container\Factories\Extension;

final class ExtensionPass implements ProcessingPassInterface
{
    /**
     * The id of the container entry to extend.
     *
     * @var string
     */
    private $id;

    /**
     * The extension.
     *
     * @var callable
     */
    private $extension;

    /**
     * Return a new ExtensionPass from the given id and extension.
     *
     * @param string    $id
     * @param callable  $extension
     * @return \Quanta\Container\Passes\ExtensionPass
     */
    public static function instance(string $id, callable $extension): ExtensionPass
    {
        return new self($id, $extension);
    }

    /**
     * Constructor.
     *
     * @param string    $id
     * @param callable  $extension
     */
    public function __construct(string $id, callable $extension)
    {
        $this->id = $id;
        $this->extension = $extension;
    }

    /**
     * @inheritdoc
     */
    public function aliases(string $id): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function tags(string ...$ids): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function processed(string $id, callable $factory): callable
    {
        return $id == $this->id
            ? new Extension($factory, $this->extension)
            : $factory;
    }
}
