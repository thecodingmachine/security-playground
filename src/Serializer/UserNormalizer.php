<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Company;
use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function assert;

final class UserNormalizer implements NormalizerInterface
{
    public function normalize($object, string|null $format = null, array $context = []): array
    {
        assert($object instanceof User);

        return [
            'firstName' => $object->getFirstName(),
            'lastName' => $object->getLastName(),
            'email' => $object->getEmail(),
        ];
    }

    /**
     * Check whether normalizing should be performed on given object.
     *
     * @param mixed $data
     */
    public function supportsNormalization($data, string|null $format = null): bool
    {
        return $data instanceof User;
    }
}
