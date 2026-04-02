<?php
/**
 * Testes do LoginForm e RegisterForm
 */

namespace tests\unit;

use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\User;
use tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Testa login com credenciais válidas.
     */
    public function testLoginWithValidCredentials(): void
    {
        $this->createTestUser([
            'email' => 'login@dever.io',
            'password' => 'senha123',
        ]);

        $form = new LoginForm();
        $form->email = 'login@dever.io';
        $form->password = 'senha123';

        $this->assertTrue($form->validate());
    }

    /**
     * Testa login com senha incorreta.
     */
    public function testLoginWithInvalidPassword(): void
    {
        $this->createTestUser([
            'email' => 'login2@dever.io',
            'password' => 'senha123',
        ]);

        $form = new LoginForm();
        $form->email = 'login2@dever.io';
        $form->password = 'senhaErrada';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('password', $form->errors);
    }

    /**
     * Testa login com email inexistente.
     */
    public function testLoginWithNonExistentEmail(): void
    {
        $form = new LoginForm();
        $form->email = 'naoexiste@dever.io';
        $form->password = 'qualquer';

        $this->assertFalse($form->validate());
    }

    /**
     * Testa validação de campos obrigatórios no login.
     */
    public function testLoginRequiredFields(): void
    {
        $form = new LoginForm();
        $this->assertFalse($form->validate());

        $this->assertArrayHasKey('email', $form->errors);
        $this->assertArrayHasKey('password', $form->errors);
    }

    /**
     * Testa registro com dados válidos.
     */
    public function testRegisterWithValidData(): void
    {
        $form = new RegisterForm();
        $form->name = 'Novo Usuário';
        $form->email = 'novo@dever.io';
        $form->password = 'senha123';
        $form->password_confirm = 'senha123';

        $user = $form->register();
        $this->assertNotNull($user);
        $this->assertNotNull($user->id);
        $this->assertEquals('novo@dever.io', $user->email);
    }

    /**
     * Testa registro com email duplicado.
     */
    public function testRegisterWithDuplicateEmail(): void
    {
        $this->createTestUser(['email' => 'existente@dever.io']);

        $form = new RegisterForm();
        $form->name = 'Outro Usuário';
        $form->email = 'existente@dever.io';
        $form->password = 'senha123';
        $form->password_confirm = 'senha123';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('email', $form->errors);
    }

    /**
     * Testa registro com senhas diferentes.
     */
    public function testRegisterWithPasswordMismatch(): void
    {
        $form = new RegisterForm();
        $form->name = 'Usuário';
        $form->email = 'mismatch@dever.io';
        $form->password = 'senha123';
        $form->password_confirm = 'outraSenha';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('password_confirm', $form->errors);
    }

    /**
     * Testa registro com senha muito curta.
     */
    public function testRegisterWithShortPassword(): void
    {
        $form = new RegisterForm();
        $form->name = 'Usuário';
        $form->email = 'curta@dever.io';
        $form->password = '123';
        $form->password_confirm = '123';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('password', $form->errors);
    }

    /**
     * Testa validação de campos obrigatórios no registro.
     */
    public function testRegisterRequiredFields(): void
    {
        $form = new RegisterForm();
        $this->assertFalse($form->validate());

        $this->assertArrayHasKey('name', $form->errors);
        $this->assertArrayHasKey('email', $form->errors);
        $this->assertArrayHasKey('password', $form->errors);
        $this->assertArrayHasKey('password_confirm', $form->errors);
    }
}
