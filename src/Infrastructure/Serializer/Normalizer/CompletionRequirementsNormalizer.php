<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer\Normalizer;

use App\Domain\WorkoutSession\ValueObject\CompletionRequirements;
use App\Domain\WorkoutSession\ValueObject\MinSetsCompletionRequirement;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CompletionRequirementsNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function getSupportedTypes(?string $format): array
    {
        return [
            CompletionRequirements::class => true,
            '*' => false,
        ];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof CompletionRequirements;
    }

    /**
     * @param CompletionRequirements $object
     *
     * @return array<int, array{exerciseCode:string,minSets:int}>
     */
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $normalizer = new MinSetsCompletionRequirementNormalizer();

        return \array_map(static fn(MinSetsCompletionRequirement $req): array => $normalizer->normalize($req, $format, $context), $object->minSetsCompletionRequirements);
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return CompletionRequirements::class === $type && \is_array($data);
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (!\is_array($data)) {
            throw new InvalidArgumentException('CompletionRequirements denormalizer expects array.');
        }

        $normalizer = new MinSetsCompletionRequirementNormalizer();
        $requirements = [];
        foreach ($data as $item) {
            if (!\is_array($item)) {
                throw new InvalidArgumentException('Invalid structure inside CompletionRequirements array.');
            }

            $requirements[] = $normalizer->denormalize($item, MinSetsCompletionRequirement::class, $format, $context);
        }

        return new CompletionRequirements($requirements);
    }
}
