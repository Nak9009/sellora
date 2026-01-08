<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ApiException extends Exception
{
    public function __construct(
        public string $message,
        public int $status = 400,
        public ?array $errors = null,
        Exception $previous = null
    ) {
        parent::__construct($message, $status, $previous);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
            'errors' => $this->errors,
        ], $this->status);
    }
}
