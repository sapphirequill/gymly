<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer\Normalizer;

use App\Domain\Shared\ValueObject\ExerciseCode;
use App\Domain\WorkoutSession\ValueObject\MinSetsCompletionRequirement;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class MinSetsCompletionRequirementNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function getSupportedTypes(?string $format): array
    {
        return [
            MinSetsCompletionRequirement::class => true,
            '*' => false,
        ];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof MinSetsCompletionRequirement;
    }

    /**
     * @param MinSetsCompletionRequirement $object
     *
     * @return array{exerciseCode:string,minSets:int}
     */
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        return [
            'exerciseCode' => new ExerciseCodeNormalizer()->normalize($object->exerciseCode),
            'minSets' => $object->minSets,
        ];
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return MinSetsCompletionRequirement::class === $type && \is_array($data);
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (!\is_array($data)) {
            throw new InvalidArgumentException('MinSetsCompletionRequirement denormalizer expects array.');
        }

        if (!isset($data['exerciseCode'], $data['minSets'])) {
            throw new InvalidArgumentException('Missing keys for MinSetsCompletionRequirement denormalization.');
        }

        return new MinSetsCompletionRequirement(
            new ExerciseCodeNormalizer()->denormalize($data['exerciseCode'], ExerciseCode::class, $format, $context),
            (int) $data['minSets']
        );
    }
}
