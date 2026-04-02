<?php
/**
 * Testes unitários do Model User
 *
 * Testa criação, validação de senha, busca por email
 * e implementação do IdentityInterface.
 */

namespace tests\unit;

use app\models\User;
use tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Testa criação de usuário com dados válidos.
     */
    public function testCreateUser(): void
    {
        $user = $this->createTestUser([
            'name' => 'Maria Silva',
            'email' => 'maria@dever.io',
            'password' => 'senha123',
        ]);

        $this->assertNotNull($user->id);
        $this->assertEquals('Maria Silva', $user->name);
        $this->assertEquals('maria@dever.io', $user->email);
        $this->assertEquals(User::STATUS_ACTIVE, $user->status);
        $this->assertNotEmpty($user->auth_key);
    }

    /**
     * Testa validação de senha.
     */
    public function testValidatePassword(): void
    {
        $user = $this->createTestUser(['password' => 'minhaSenha123']);

        $this->assertTrue($user->validatePassword('minhaSenha123'));
        $this->assertFalse($user->validatePassword('senhaErrada'));
    }

    /**
     * Testa busca por email.
     */
    public function testFindByEmail(): void
    {
        $this->createTestUser(['email' => 'busca@dever.io']);

        $found = User::findByEmail('busca@dever.io');
        $this->assertNotNull($found);
        $this->assertEquals('busca@dever.io', $found->email);

        $notFound = User::findByEmail('naoexiste@dever.io');
        $this->assertNull($notFound);
    }

    /**
     * Testa findIdentity.
     */
    public function testFindIdentity(): void
    {
        $user = $this->createTestUser();
        $found = User::findIdentity($user->id);

        $this->assertNotNull($found);
        $this->assertEquals($user->id, $found->id);
    }

    /**
     * Testa validação de auth_key.
     */
    public function testValidateAuthKey(): void
    {
        $user = $this->createTestUser();

        $this->assertTrue($user->validateAuthKey($user->auth_key));
        $this->assertFalse($user->validateAuthKey('chave_invalida'));
    }

    /**
     * Testa rejeição de email duplicado.
     */
    public function testDuplicateEmailRejected(): void
    {
        $this->createTestUser(['email' => 'duplicado@dever.io']);

        $user2 = new User();
        $user2->name = 'Outro Usuário';
        $user2->email = 'duplicado@dever.io';
        $user2->setPassword('senha123');
        $user2->generateAuthKey();

        $this->assertFalse($user2->validate());
        $this->assertArrayHasKey('email', $user2->errors);
    }

    /**
     * Testa validação de campos obrigatórios.
     */
    public function testRequiredFields(): void
    {
        $user = new User();
        $this->assertFalse($user->validate());

        $this->assertArrayHasKey('name', $user->errors);
        $this->assertArrayHasKey('email', $user->errors);
    }

    /**
     * Testa método getInitials.
     */
    public function testGetInitials(): void
    {
        $user = $this->createTestUser(['name' => 'João Pedro']);
        $this->assertEquals('JP', $user->getInitials());

        $user2 = $this->createTestUser(['name' => 'Ana', 'email' => 'ana@test.io']);
        $this->assertEquals('AN', $user2->getInitials());
    }
}
