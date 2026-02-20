<?php

declare(strict_types=1);

namespace Continuum\Component\Dog;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @see https://dog.ceo/dog-api/
 */
final readonly class DogClient
{
    private const string API_URL = 'https://dog.ceo/api';

    public function __construct(
        private HttpClientInterface $client,
    ) {}

    /**
     * @return non-empty-string
     */
    public function getBlenheimSpanielRandomImage(): string
    {
        return $this->getBreedRandomImage('spaniel/blenheim');
    }

    /**
     * @param non-empty-string $breed
     *
     * @return non-empty-string
     */
    public function getBreedRandomImage(string $breed): string
    {
        /**
         * @var array{message: non-empty-string, status: string} $data
         */
        $data = $this->sendRequest('/breed/' . $breed . '/images/random');

        return $data['message'];
    }

    /**
     * @return non-empty-string
     */
    public function getRandomImage(): string
    {
        /**
         * @var array{message: non-empty-string, status: string} $data
         */
        $data = $this->sendRequest('/breeds/image/random');

        return $data['message'];
    }

    /**
     * @param non-empty-string $path
     *
     * @return array<string, mixed>
     */
    private function sendRequest(string $path): array
    {
        $response = $this->client->request('GET', self::API_URL . $path);

        if (200 !== $response->getStatusCode()) {
            throw new BadRequestHttpException('Something went wrong.');
        }

        /** @var array<string, mixed> */
        return $response->toArray();
    }
}
