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
     * Testa atualização de perfil: nome e e-mail.
     */
    public function testUpdateProfile(): void
    {
        $user = $this->createTestUser(['name' => 'Felipe', 'email' => 'felipe@dever.io']);
        $user->scenario = 'update';
        $user->name = 'Felipe Atualizado';
        $user->email = 'felipe.atualizado@dever.io';

        $this->assertTrue($user->validate());
        $this->assertTrue($user->save(false));

        $updated = User::findOne($user->id);
        $this->assertEquals('Felipe Atualizado', $updated->name);
        $this->assertEquals('felipe.atualizado@dever.io', $updated->email);
    }

    /**
     * Testa que não aceita e-mail duplicado ao atualizar.
     */
    public function testUpdateProfileDuplicateEmailRejected(): void
    {
        $this->createTestUser(['email' => 'existente@dever.io']);
        $user = $this->createTestUser(['email' => 'usuario@dever.io']);

        $user->scenario = 'update';
        $user->email = 'existente@dever.io';

        $this->assertFalse($user->validate());
        $this->assertArrayHasKey('email', $user->errors);
    }

    /**
     * Testa validação de senha mínima na atualização de perfil.
     */
    public function testUpdateProfilePasswordMinLength(): void
    {
        $user = $this->createTestUser(['email' => 'senha@dever.io']);

        $user->scenario = 'update';
        $user->password = '123';

        $this->assertFalse($user->validate());
        $this->assertArrayHasKey('password', $user->errors);
    }

    /**
     * Testa validação de confirmação de senha.
     */
    public function testUpdateProfilePasswordConfirmation(): void
    {
        $user = $this->createTestUser(['email' => 'confirma@dever.io']);

        $user->scenario = 'update';
        $user->password = 'senhaSegura123';
        $user->password_repeat = 'senhaDiferente';

        $this->assertFalse($user->validate());
        $this->assertArrayHasKey('password_repeat', $user->errors);
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
