<?php

declare(strict_types=1);

namespace Continuum\Tests\Controller\Security;

use Continuum\Controller\Security\LoginController;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(LoginController::class)]
final class LoginControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    #[Override]
    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testLoginWrongUsername(): void
    {
        // Denied - Can't log in with invalid identifier.
        $this->client->request(Request::METHOD_GET, '/login');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Sign in', [
            '_username' => 'does_not_exist_username',
            '_password' => 'password',
        ]);

        self::assertResponseStatusCodeSame(302);
        self::assertResponseRedirects('/login');

        $this->client->followRedirect();

        // Ensure we do not reveal if the user exists or not.
        self::assertSelectorTextContains('.alert-danger:not(.browser-unsupport)', 'Invalid credentials.');
        self::assertInputValueSame('_username', 'does_not_exist_username');
        self::assertInputValueSame('_password', '');
    }

    public function testLoginWrongPassword(): void
    {
        // Denied - Can't log in with invalid password.
        $this->client->request(Request::METHOD_GET, '/login');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Sign in', [
            '_username' => 'admin',
            '_password' => 'bad-password',
        ]);

        self::assertResponseStatusCodeSame(302);
        self::assertResponseRedirects('/login');

        $this->client->followRedirect();

        // Ensure we do not reveal the user exists but the password is wrong.
        self::assertSelectorTextContains('.alert-danger:not(.browser-unsupport)', 'Invalid credentials.');
        self::assertInputValueSame('_username', 'admin');
        self::assertInputValueSame('_password', '');
    }

    public function testLogin(): void
    {
        $this->client->request(Request::METHOD_GET, '/login');
        self::assertResponseIsSuccessful();

        // Success - Login with valid credentials is allowed.
        $this->client->submitForm('Sign in', [
            '_username' => 'admin',
            '_password' => 'password',
        ]);

        self::assertResponseRedirects('/');
        $this->client->followRedirect();

        self::assertSelectorNotExists('.alert-danger');
        // self::assertSelectorExists('body#homepage');
    }
}
