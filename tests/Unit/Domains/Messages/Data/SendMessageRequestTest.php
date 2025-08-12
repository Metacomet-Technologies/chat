<?php

declare(strict_types=1);

use App\Domains\Messages\Data\V1\SendMessageRequest;
use Spatie\LaravelData\ValidationException;

it('validates required content field', function () {
    expect(fn () => SendMessageRequest::from([]))
        ->toThrow(ValidationException::class);
});

it('validates content max length', function () {
    $longContent = str_repeat('a', 5001);

    expect(fn () => SendMessageRequest::from(['content' => $longContent]))
        ->toThrow(ValidationException::class);
});

it('creates dto with valid data', function () {
    $data = [
        'content' => 'Hello, world!',
        'type' => 'text',
        'metadata' => ['key' => 'value'],
    ];

    $dto = SendMessageRequest::from($data);

    expect($dto->content)->toBe('Hello, world!')
        ->and($dto->type)->toBe('text')
        ->and($dto->metadata)->toBe(['key' => 'value']);
});

it('sets default type to text', function () {
    $dto = SendMessageRequest::from(['content' => 'Hello']);

    expect($dto->type)->toBe('text');
});

it('sets metadata to null by default', function () {
    $dto = SendMessageRequest::from(['content' => 'Hello']);

    expect($dto->metadata)->toBeNull();
});
