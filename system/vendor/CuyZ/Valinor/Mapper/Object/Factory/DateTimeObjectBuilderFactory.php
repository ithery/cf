<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Mapper\Object\Factory;

use DateTime;
use function count;
use DateTimeImmutable;
use function array_filter;
use CuyZ\Valinor\Type\ObjectType;
use CuyZ\Valinor\Library\Settings;
use CuyZ\Valinor\Definition\FunctionObject;
use CuyZ\Valinor\Definition\ClassDefinition;
use CuyZ\Valinor\Mapper\Object\ObjectBuilder;
use CuyZ\Valinor\Mapper\Object\FunctionObjectBuilder;
use CuyZ\Valinor\Mapper\Object\DateTimeFormatConstructor;

use CuyZ\Valinor\Mapper\Object\NativeConstructorObjectBuilder;
use CuyZ\Valinor\Definition\Repository\FunctionDefinitionRepository;

/** @internal */
final class DateTimeObjectBuilderFactory implements ObjectBuilderFactory {
    private ObjectBuilderFactory $delegate;

    private array $supportedDateFormats;

    private FunctionDefinitionRepository $functionDefinitionRepository;

    public function __construct(
        ObjectBuilderFactory $delegate,
        /** @var non-empty-list<non-empty-string> */
        array $supportedDateFormats,
        FunctionDefinitionRepository $functionDefinitionRepository
    ) {
        $this->delegate = $delegate;
        $this->supportedDateFormats = $supportedDateFormats;
        $this->functionDefinitionRepository = $functionDefinitionRepository;
    }

    public function for(ClassDefinition $class): array {
        $className = $class->name;

        $builders = $this->delegate->for($class);

        if ($className !== DateTime::class && $className !== DateTimeImmutable::class) {
            return $builders;
        }

        // Remove `DateTime` & `DateTimeImmutable` native constructors
        $builders = array_filter($builders, fn (ObjectBuilder $builder) => !$builder instanceof NativeConstructorObjectBuilder);

        $buildersWithOneArgument = array_filter($builders, fn (ObjectBuilder $builder) => count($builder->describeArguments()) === 1);

        if (count($buildersWithOneArgument) === 0 || $this->supportedDateFormats !== Settings::DEFAULT_SUPPORTED_DATETIME_FORMATS) {
            $builders[] = $this->internalDateTimeBuilder($class->type);
        }

        /** @var non-empty-list<ObjectBuilder> */
        return $builders;
    }

    private function internalDateTimeBuilder(ObjectType $type): FunctionObjectBuilder {
        $constructor = new DateTimeFormatConstructor(...$this->supportedDateFormats);
        $function = new FunctionObject($this->functionDefinitionRepository->for($constructor), $constructor);

        return new FunctionObjectBuilder($function, $type);
    }
}
