<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer\Normalizer;

use App\Domain\Shared\ValueObject\ExerciseCode;
use App\Domain\WorkoutSession\ValueObject\Weight;
use App\Domain\WorkoutSession\ValueObject\WorkoutSet;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class WorkoutSetNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function getSupportedTypes(?string $format): array
    {
        return [
            WorkoutSet::class => true,
            '*' => false,
        ];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof WorkoutSet;
    }

    /**
     * @param WorkoutSet $object
     *
     * @return array{exerciseCode:string,repetitions:int,weight:array{value:float,unit:string}}
     */
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $weightData = new WeightNormalizer()->normalize($object->weight, $format, $context);

        return [
            'exerciseCode' => new ExerciseCodeNormalizer()->normalize($object->exerciseCode),
            'repetitions' => $object->repetitions,
            'weight' => $weightData,
        ];
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return WorkoutSet::class === $type && \is_array($data);
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (!\is_array($data)) {
            throw new InvalidArgumentException('WorkoutSet denormalizer expects array.');
        }

        foreach (['exerciseCode', 'repetitions', 'weight'] as $key) {
            if (!\array_key_exists($key, $data)) {
                throw new InvalidArgumentException('Missing keys for WorkoutSet denormalization.');
            }
        }

        if (!\is_array($data['weight']) || !isset($data['weight']['value'], $data['weight']['unit'])) {
            throw new InvalidArgumentException('Invalid weight structure for WorkoutSet denormalization.');
        }

        $weight = new WeightNormalizer()->denormalize($data['weight'], Weight::class, $format, $context);

        return new WorkoutSet(
            new ExerciseCodeNormalizer()->denormalize($data['exerciseCode'], ExerciseCode::class, $format, $context),
            (int) $data['repetitions'],
            $weight
        );
    }
}
