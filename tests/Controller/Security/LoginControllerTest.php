<?php

declare(strict_types=1);

namespace Continuum\Tests\Controller\Security;

use Continuum\Controller\Security\LoginController;
use Continuum\Entity\User;
use Continuum\Security\User\UserStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(LoginController::class)]
final class LoginControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $container = self::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository(User::class);

        // Remove any existing users from the test database
        foreach ($userRepository->findAll() as $user) {
            $em->remove($user);
        }

        $em->flush();

        // Create a User fixture
        $user = new User('email@example.com');
        $user->password = $container->get('security.user_password_hasher')->hashPassword($user, 'password');
        $user->status = UserStatus::Active;

        $em->persist($user);
        $em->flush();
    }

    public function testLogin(): void
    {
        // Denied - Can't log in with invalid email address.
        $this->client->request(Request::METHOD_GET, '/login');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Sign in', [
            '_username' => 'doesNotExist@example.com',
            '_password' => 'password',
        ]);

        self::assertResponseRedirects('/login');
        $this->client->followRedirect();

        // Ensure we do not reveal if the user exists or not.
        self::assertSelectorTextContains('.alert-danger:not(.browser-unsupport)', 'Invalid credentials.');
        self::assertInputValueSame('_username', 'doesNotExist@example.com');

        // Denied - Can't log in with invalid password.
        $this->client->request(Request::METHOD_GET, '/login');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Sign in', [
            '_username' => 'email@example.com',
            '_password' => 'bad-password',
        ]);

        self::assertResponseRedirects('/login');
        $this->client->followRedirect();

        // Ensure we do not reveal the user exists but the password is wrong.
        self::assertSelectorTextContains('.alert-danger:not(.browser-unsupport)', 'Invalid credentials.');
        self::assertInputValueSame('_username', 'email@example.com');

        // Success - Login with valid credentials is allowed.
        $this->client->submitForm('Sign in', [
            '_username' => 'email@example.com',
            '_password' => 'password',
        ]);

        self::assertResponseRedirects('/');
        $this->client->followRedirect();

        self::assertSelectorNotExists('.alert-danger');
        // self::assertSelectorExists('body#homepage');
    }
}
