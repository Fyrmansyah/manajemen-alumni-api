<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ResponseBuilder
{
    protected string $status;
    protected string $message;
    protected mixed $data;
    protected mixed $errors;
    protected int $httpCode;

    public static function success(): self
    {
        return (new self())
            ->status('success')
            ->httpCode(Response::HTTP_OK);
    }

    public static function fail(): self
    {
        return (new self())
            ->status('fail')
            ->httpCode(Response::HTTP_BAD_REQUEST);
    }

    public function build(): JsonResponse
    {
        return response()->json([
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
            'erros' => $this->errors,
        ], $this->httpCode);
    }

    public function status(string $status)
    {
        $this->status = $status;
        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function data(mixed $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function errors(mixed $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    public function httpCode(int $httpCode): self
    {
        $this->httpCode = $httpCode;
        return $this;
    }
}
