<?php

namespace App\Services;

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
}
