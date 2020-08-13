<?php

namespace App\Services;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Normalize
{
    /**
     * Better api validation format.
     *
     * @param $errors
     */
    public function transformSymfonyValidation($errors): array
    {
        $messages = [];
        foreach ($errors as $violation) {
            $messages[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return $messages;
    }

    /**
     * Convert a doctrine entity to json and avoid circular references.
     *
     * @return string
     */
    public function entitiyToJson($entiy)
    {
        $encoder = new JsonEncoder();

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);

        $serializer = new Serializer([$normalizer], [$encoder]);

        return $serializer->serialize($entiy, 'json');
    }
}
