<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestCheckerService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function check(array $data, array $requiredFields): void
    {
        $missingFields = array_diff($requiredFields, array_keys($data));
        if (!empty($missingFields)) {
            throw new BadRequestException('Missing required fields: ' . implode(', ', $missingFields));
        }
    }

    public function validateRequestDataByConstraints(array $data, array $constraints): void
    {
        $validationErrors = $this->validator->validate($data, new Collection(['fields' => $constraints]));
        if (count($validationErrors) > 0) {
            $errors = [];
            foreach ($validationErrors as $error) {
                $errors[$error->getPropertyPath()] = $error->getMessage();
            }
            throw new UnprocessableEntityHttpException(json_encode($errors));
        }
    }
}