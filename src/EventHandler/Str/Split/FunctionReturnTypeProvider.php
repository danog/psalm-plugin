<?php

declare(strict_types=1);

namespace Psl\Psalm\EventHandler\Str\Split;

use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psl\Psalm\Argument;

final class FunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    /**
     * @return non-empty-list<lowercase-string>
     */
    public static function getFunctionIds(): array
    {
        return [
            'psl\str\split',
            'psl\str\byte\split',
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Type\Union
    {
        $argument_type = Argument::getType($event->getCallArgs(), $event->getStatementsSource(), 0);
        if (null === $argument_type) {
            // [unknown] -> list<string>
            $value = new Type\Union([new Type\Atomic\TString()], ['possibly_undefined' => true]);
            return new Type\Union([new Type\Atomic\TKeyedArray(
                [
                    0 => $value
                ],
                null,
                [
                    Type::getListKey(),
                    $value
                ],
                true
            )]);
        }

        $string_argument_type = $argument_type->getAtomicTypes()['string'] ?? null;
        if (null === $string_argument_type) {
            // [unknown] -> list<string>
            $value = new Type\Union([new Type\Atomic\TString()], ['possibly_undefined' => true]);
            return new Type\Union([new Type\Atomic\TKeyedArray(
                [
                    0 => $value
                ],
                null,
                [
                    Type::getListKey(),
                    $value
                ],
                true
            )]);
        }

        if ($string_argument_type instanceof Type\Atomic\TNonEmptyString) {
            // non-empty-lowercase-string => non-empty-list<non-empty-lowercase-string>
            if ($string_argument_type instanceof Type\Atomic\TNonEmptyLowercaseString) {
                $value = new Type\Union([new Type\Atomic\TNonEmptyLowercaseString()]);
                return new Type\Union([new Type\Atomic\TKeyedArray(
                    [
                        0 => $value
                    ],
                    null,
                    [
                        Type::getListKey(),
                        $value
                    ],
                    true
                )]);
            }

            // non-empty-string => non-empty-list<non-empty-string>
            $value = new Type\Union([new Type\Atomic\TNonEmptyString()]);
            return new Type\Union([new Type\Atomic\TKeyedArray(
                [
                    0 => $value
                ],
                null,
                [
                    Type::getListKey(),
                    $value
                ],
                true
            )]);
        }

        // string -> list<string>
        $value = new Type\Union([new Type\Atomic\TString()], ['possibly_undefined' => true]);
        return new Type\Union([new Type\Atomic\TKeyedArray(
            [
                0 => $value
            ],
            null,
            [
                Type::getListKey(),
                $value
            ],
            true
        )]);
    }
}
