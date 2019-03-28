<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\FactoryMap;
use Quanta\Container\MergedFactoryMap;
use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ParameterFactoryMap;
use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Alias;
use Quanta\Container\Factories\Factory;
use Quanta\Container\Factories\Invokable;
use Quanta\Container\Factories\Extension;
use Quanta\Container\Configuration\Passes\Tagging;
use Quanta\Container\Configuration\Passes\TaggingPass;
use Quanta\Container\Configuration\Passes\ExtensionPass;
use Quanta\Container\Configuration\Passes\MergedProcessingPass;
use Quanta\Container\Configuration\Passes\ProcessingPassInterface;

final class ArrayConfigurationUnit implements ConfigurationUnitInterface
{
    /**
     * The value factory used to parse parameters.
     *
     * @var \Quanta\Container\Values\ValueFactory
     */
    private $factory;

    /**
     * The configuration array.
     *
     * @var array
     */
    private $configuration;

    /**
     * The source of the array.
     *
     * Displayed in exception message when not empty.
     *
     * @var string
     */
    private $source;

    /**
     * Return a new ArrayConfigurationUnit from the given value factory,
     * configuration array and source.
     *
     * @param \Quanta\Container\Values\ValueFactory $factory
     * @param array                                 $configuration
     * @param string                                $source
     * @return \Quanta\Container\Configuration\ArrayConfigurationUnit
     */
    public static function instance(ValueFactory $factory, array $configuration, string $source = ''): self
    {
        return new self($factory, $configuration, $source);
    }

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Values\ValueFactory $factory
     * @param array                                 $configuration
     * @param string                                $source
     */
    public function __construct(ValueFactory $factory, array $configuration, string $source = '')
    {
        $this->factory = $factory;
        $this->configuration = $configuration;
        $this->source = $source;
    }

    /**
     * @inheritdoc
     */
    public function map(): FactoryMapInterface
    {
        $result = \Quanta\ArrayTypeCheck::nested($this->configuration, [
            'parameters' => '*',
            'aliases' => 'string',
            'invokables' => 'string',
            'factories' => 'callable',
        ]);

        if (! $result->isValid()) {
            throw new \UnexpectedValueException(
                $this->arrayNotValidErrorMessage($result)
            );
        }

        $configuration = $result->sanitized();

        $maps[] = new ParameterFactoryMap($this->factory, $configuration['parameters']);
        $maps[] = new FactoryMap(array_map([Alias::class, 'instance'], $configuration['aliases']));
        $maps[] = new FactoryMap(array_map([Invokable::class, 'instance'], $configuration['invokables']));
        $maps[] = new FactoryMap($configuration['factories']);

        return new MergedFactoryMap(...$maps);
    }

    /**
     * @inheritdoc
     */
    public function pass(): ProcessingPassInterface
    {
        $result = \Quanta\ArrayTypeCheck::nested($this->configuration, [
            'extensions' => 'callable',
            'tags.*' => 'string',
            'mappers' => 'string',
            'passes' => ProcessingPassInterface::class,
        ]);

        if (! $result->isValid()) {
            throw new \UnexpectedValueException(
                $this->arrayNotValidErrorMessage($result)
            );
        }

        $configuration = $result->sanitized();

        $passes[] = array_map([$this, 'taggingPass'], ...[
            array_keys($configuration['tags']),
            $configuration['tags'],
        ]);

        $passes[] = array_map([$this, 'reverseTaggingPass'], ...[
            array_keys($configuration['mappers']),
            $configuration['mappers'],
        ]);

        $passes[] = array_map([ExtensionPass::class, 'instance'], ...[
            array_keys($configuration['extensions']),
            $configuration['extensions'],
        ]);

        $passes[] = array_values($configuration['passes']);

        return new MergedProcessingPass(...array_merge(...$passes));
    }

    /**
     * Return a tagging pass associating the given id to the entries with the
     * given ids (manual tagging).
     *
     * @param string    $id
     * @param string[]  $ids
     * @return \Quanta\Container\Configuration\Passes\TaggingPass
     */
    private function taggingPass(string $id, array $ids): TaggingPass
    {
        return new TaggingPass($id, new Tagging\Entries(...array_values($ids)));
    }

    /**
     * Return a tagging pass associating the given id to the entries with a
     * subclass of the given class as id (reverse tagging).
     *
     * @param string $id
     * @param string $class
     * @return \Quanta\Container\Configuration\Passes\TaggingPass
     */
    private function reverseTaggingPass(string $id, string $class): TaggingPass
    {
        return new TaggingPass($id, new Tagging\Implementations($class));
    }

    /**
     * Return the message of exceptions thrown when the given array type check
     * result is not valid.
     *
     * @param \Quanta\ArrayTypeCheck\ResultInterface $result
     */
    private function arrayNotValidErrorMessage(\Quanta\ArrayTypeCheck\ResultInterface $result): string
    {
        $message = $result->message()->source('configuration array');

        return $this->source != ''
            ? sprintf('%s (%s)', $message, $this->source)
            : $message;
    }
}
