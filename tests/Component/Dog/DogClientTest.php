<?php

declare(strict_types=1);

namespace Continuum\Tests\Component\Dog;

use Continuum\Component\Dog\DogClient;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[CoversClass(DogClient::class)]
final class DogClientTest extends TestCase
{
    private HttpClientInterface&MockObject $client;
    private DogClient $dogClient;

    #[Override]
    protected function setUp(): void
    {
        $this->client = $this->createMock(HttpClientInterface::class);
        $this->dogClient = new DogClient($this->client);
    }

    public function testGetBlenheimSpanielRandomImage(): void
    {
        $expectedImageUrl = 'https://images.dog.ceo/breeds/spaniel-blenheim/sample-image.jpg';
        $this->mockRequest(
            path: '/breed/spaniel/blenheim/images/random',
            responseData: [
                'message' => $expectedImageUrl,
                'status' => 'success',
            ]
        );

        $result = $this->dogClient->getBlenheimSpanielRandomImage();

        self::assertSame($expectedImageUrl, $result);
    }

    public function testGetBreedRandomImage(): void
    {
        $breed = 'labrador';
        $expectedImageUrl = 'https://images.dog.ceo/breeds/labrador/sample-image.jpg';
        $this->mockRequest(
            path: '/breed/labrador/images/random',
            responseData: [
                'message' => $expectedImageUrl,
                'status' => 'success',
            ]
        );

        $result = $this->dogClient->getBreedRandomImage($breed);

        self::assertSame($expectedImageUrl, $result);
    }

    public function testGetBreedRandomImageWithMultipleWordBreed(): void
    {
        $breed = 'bulldog/french';
        $expectedImageUrl = 'https://images.dog.ceo/breeds/bulldog-french/sample-image.jpg';
        $this->mockRequest(
            path: '/breed/bulldog/french/images/random',
            responseData: [
                'message' => $expectedImageUrl,
                'status' => 'success',
            ]
        );

        $result = $this->dogClient->getBreedRandomImage($breed);

        self::assertSame($expectedImageUrl, $result);
    }

    public function testGetRandomImage(): void
    {
        $expectedImageUrl = 'https://images.dog.ceo/breeds/mixed/sample-image.jpg';
        $this->mockRequest(
            path: '/breeds/image/random',
            responseData: [
                'message' => $expectedImageUrl,
                'status' => 'success',
            ]
        );

        $result = $this->dogClient->getRandomImage();

        self::assertSame($expectedImageUrl, $result);
    }

    public function testGetBreedRandomImageThrowsExceptionOnFailureStatus(): void
    {
        $this->mockRequest(
            path: '/breed/unknown/images/random',
            statusCode: 404
        );

        $this->expectExceptionObject(
            new BadRequestHttpException('Something went wrong.')
        );

        $this->dogClient->getBreedRandomImage('unknown');
    }

    public function testGetRandomImageThrowsExceptionOnServerError(): void
    {
        $this->mockRequest(
            path: '/breeds/image/random',
            statusCode: 500
        );

        $this->expectExceptionObject(
            new BadRequestHttpException('Something went wrong.')
        );

        $this->dogClient->getRandomImage();
    }

    public function testGetBlenheimSpanielRandomImageThrowsExceptionOn403(): void
    {
        $this->mockRequest(
            path: '/breed/spaniel/blenheim/images/random',
            statusCode: 403
        );

        $this->expectExceptionObject(
            new BadRequestHttpException('Something went wrong.')
        );

        $this->dogClient->getBlenheimSpanielRandomImage();
    }

    /**
     * @param null|array<string, mixed> $responseData
     */
    private function mockRequest(string $path, int $statusCode = 200, ?array $responseData = null): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn($statusCode);

        if (200 === $statusCode && null !== $responseData) {
            $response->expects($this->once())->method('toArray')->willReturn($responseData);
        }

        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dog.ceo/api' . $path)
            ->willReturn($response);
    }
}
