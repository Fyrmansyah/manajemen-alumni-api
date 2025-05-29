<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ResponseBuilder
{
    protected ?string $status = null;
    protected ?string $message = null;
    protected mixed $data = null;
    protected mixed $errors = null;
    protected ?int $httpCode = null;
    protected mixed $pagination = null;

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
        $response = [];

        if (!is_null($this->status)) {
            $response['status'] = $this->status;
        }

        if (!is_null($this->message)) {
            $response['message'] = $this->message;
        }

        if (!is_null($this->data)) {
            $response['data'] = $this->data;
        }

        if (!is_null($this->errors)) {
            $response['errors'] = $this->errors;
        }

        if (!is_null($this->pagination)) {
            $response['pagination'] = $this->pagination;
        }

        return response()->json($response, $this->httpCode);
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

    public function pagination(?string $nextPageUrl, ?string $previousPageUrl): self
    {
        $this->pagination = [
            'next_page_url' => $nextPageUrl,
            'previous_page_url' => $previousPageUrl,
        ];
        return $this;
    }
}
