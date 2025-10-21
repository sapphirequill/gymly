<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer\Normalizer;

use App\Domain\WorkoutSession\ValueObject\Weight;
use App\Domain\WorkoutSession\ValueObject\WeightUnit;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class WeightNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function getSupportedTypes(?string $format): array
    {
        return [
            Weight::class => true,
            '*' => false,
        ];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Weight;
    }

    /**
     * @return array{value:float,unit:string}
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        if (!$data instanceof Weight) {
            throw new InvalidArgumentException('Weight normalizer expects Weight instance.');
        }

        return [
            'value' => $data->value,
            'unit' => $data->unit->getLabel(),
        ];
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return Weight::class === $type && \is_array($data);
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (!\is_array($data)) {
            throw new InvalidArgumentException('Weight denormalizer expects array.');
        }

        if (!isset($data['value'], $data['unit'])) {
            throw new InvalidArgumentException('Missing keys for Weight denormalization.');
        }

        return new Weight((float) $data['value'], WeightUnit::fromLabel((string) $data['unit']));
    }
}
