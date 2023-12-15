<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Company;
use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function assert;

final class CompanyNormalizer implements NormalizerInterface
{
    public function __construct(private readonly UserNormalizer $userNormalizer)
    {

    }

    public function normalize($object, string|null $format = null, array $context = []): array
    {
        assert($object instanceof Company);

        return [
            'name' => $object->getName(),
            'users' => $object->getUsers()->map(fn (User $user) => $this->userNormalizer->normalize($user))->toArray(),
        ];
    }

    /**
     * Check whether normalizing should be performed on given object.
     *
     * @param mixed $data
     */
    public function supportsNormalization($data, string|null $format = null): bool
    {
        return $data instanceof Company;
    }
}
