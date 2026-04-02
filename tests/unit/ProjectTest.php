<?php
/**
 * Testes unitários do Model Project
 *
 * Testa criação, autorização (dono/membro),
 * gerenciamento de membros e progresso.
 */

namespace tests\unit;

use app\models\Project;
use app\models\ProjectUser;
use tests\TestCase;

class ProjectTest extends TestCase
{
    /**
     * Testa criação de projeto.
     */
    public function testCreateProject(): void
    {
        $user = $this->createTestUser();
        $project = $this->createTestProject($user, [
            'title' => 'Meu Projeto',
            'description' => 'Descrição do projeto teste',
        ]);

        $this->assertNotNull($project->id);
        $this->assertEquals('Meu Projeto', $project->title);
        $this->assertEquals($user->id, $project->owner_id);
        $this->assertEquals('active', $project->status);
    }

    /**
     * Testa verificação de dono do projeto.
     */
    public function testIsOwner(): void
    {
        $owner = $this->createTestUser(['email' => 'dono@dever.io']);
        $other = $this->createTestUser(['email' => 'outro@dever.io']);
        $project = $this->createTestProject($owner);

        $this->assertTrue($project->isOwner($owner->id));
        $this->assertFalse($project->isOwner($other->id));
    }

    /**
     * Testa verificação de membro do projeto.
     */
    public function testIsMember(): void
    {
        $owner = $this->createTestUser(['email' => 'dono2@dever.io']);
        $member = $this->createTestUser(['email' => 'membro@dever.io']);
        $stranger = $this->createTestUser(['email' => 'estranho@dever.io']);

        $project = $this->createTestProject($owner);

        // Owner é membro
        $this->assertTrue($project->isMember($owner->id));

        // Estranho não é membro
        $this->assertFalse($project->isMember($stranger->id));

        // Adicionar membro
        $project->addMember($member->id);
        $this->assertTrue($project->isMember($member->id));
    }

    /**
     * Testa controle de acesso ao projeto.
     */
    public function testHasAccess(): void
    {
        $owner = $this->createTestUser(['email' => 'dono3@dever.io']);
        $member = $this->createTestUser(['email' => 'membro2@dever.io']);
        $stranger = $this->createTestUser(['email' => 'estranho2@dever.io']);

        $project = $this->createTestProject($owner);
        $project->addMember($member->id);

        $this->assertTrue($project->hasAccess($owner->id));
        $this->assertTrue($project->hasAccess($member->id));
        $this->assertFalse($project->hasAccess($stranger->id));
    }

    /**
     * Testa adição de membro duplicado (deve retornar false).
     */
    public function testAddDuplicateMember(): void
    {
        $owner = $this->createTestUser(['email' => 'dono4@dever.io']);
        $member = $this->createTestUser(['email' => 'membro3@dever.io']);

        $project = $this->createTestProject($owner);
        $this->assertTrue($project->addMember($member->id));
        $this->assertFalse($project->addMember($member->id)); // Duplicado
    }

    /**
     * Testa remoção de membro.
     */
    public function testRemoveMember(): void
    {
        $owner = $this->createTestUser(['email' => 'dono5@dever.io']);
        $member = $this->createTestUser(['email' => 'membro4@dever.io']);

        $project = $this->createTestProject($owner);
        $project->addMember($member->id);

        // Não pode remover o dono
        $this->assertFalse($project->removeMember($owner->id));

        // Pode remover membro
        $this->assertTrue($project->removeMember($member->id));
        $this->assertFalse($project->isMember($member->id));
    }

    /**
     * Testa contagem de tarefas e progresso.
     */
    public function testProgressCalculation(): void
    {
        $user = $this->createTestUser(['email' => 'dono6@dever.io']);
        $project = $this->createTestProject($user);

        // Sem tarefas = 0%
        $this->assertEquals(0, $project->getProgress());
        $this->assertEquals(0, $project->getTaskCount());
    }

    /**
     * Testa validação de título obrigatório.
     */
    public function testTitleRequired(): void
    {
        $project = new Project();
        $project->owner_id = 1;
        $this->assertFalse($project->validate(['title']));
        $this->assertArrayHasKey('title', $project->errors);
    }

    /**
     * Testa validação de datas.
     */
    public function testDateValidation(): void
    {
        $user = $this->createTestUser(['email' => 'dono7@dever.io']);
        $project = new Project();
        $project->title = 'Projeto Datas';
        $project->owner_id = $user->id;
        $project->start_date = '2025-01-01';
        $project->end_date = '2024-01-01'; // Antes do início

        $this->assertFalse($project->validate(['end_date']));
    }
}
