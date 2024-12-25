<?php

namespace App\EventListener;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

class RuntimeConstraintExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationFailedException) {
            // Get validation errors
            $violations = $exception->getViolations();
            $errors = [];

            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            $response = new JsonResponse(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        } elseif ($exception instanceof UniqueConstraintViolationException) {
            $response = new JsonResponse(
                ['error' => 'Duplicate entry detected: ' . $exception->getMessage()],
                Response::HTTP_CONFLICT // HTTP 409 Conflict
            );
        } elseif ($exception instanceof HttpExceptionInterface) {
            $response = new JsonResponse(
                ['error' => $exception->getMessage()],
                $exception->getStatusCode()
            );
        } else {
            $response = new JsonResponse(
                ['error' => 'An unexpected error occurred.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $event->setResponse($response);
    }
    private function parseUniqueConstraintMessage(string $message): array
    {
        if (preg_match('/Duplicate entry \'(.*?)\' for key \'(.*?)\'/', $message, $matches)) {
            return [
                'field' => $matches[2] ?? 'unknown',
                'value' => $matches[1] ?? 'unknown'
            ];
        }

        return ['message' => 'Unique constraint violation'];
    }
}



