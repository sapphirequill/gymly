<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer\Normalizer;

use App\Domain\Shared\ValueObject\ExerciseCode;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ExerciseCodeNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function getSupportedTypes(?string $format): array
    {
        return [
            ExerciseCode::class => true,
            '*' => false,
        ];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof ExerciseCode;
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): string
    {
        if (!$data instanceof ExerciseCode) {
            throw new InvalidArgumentException('ExerciseCode normalizer expects ExerciseCode instance.');
        }

        return (string) $data;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return ExerciseCode::class === $type && \is_string($data);
    }

    /**
     * @return ExerciseCode
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (!\is_string($data)) {
            throw new InvalidArgumentException('ExerciseCode denormalizer expects string.');
        }

        return ExerciseCode::fromCode($data);
    }
}
