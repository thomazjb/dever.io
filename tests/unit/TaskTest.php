<?php
/**
 * Testes unitários do Model Task
 *
 * Testa criação, status, prioridade, atraso,
 * conclusão e filtros de consulta.
 */

namespace tests\unit;

use app\models\Task;
use app\models\Project;
use tests\TestCase;

class TaskTest extends TestCase
{
    private $user;
    private $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createTestUser(['email' => 'task@dever.io']);
        $this->project = $this->createTestProject($this->user);
    }

    /**
     * Helper para criar tarefa de teste.
     */
    private function createTask(array $attributes = []): Task
    {
        $task = new Task();
        $task->project_id = $attributes['project_id'] ?? $this->project->id;
        $task->title = $attributes['title'] ?? 'Tarefa Teste';
        $task->description = $attributes['description'] ?? 'Descrição';
        $task->priority = $attributes['priority'] ?? Task::PRIORITY_MEDIUM;
        $task->status = $attributes['status'] ?? Task::STATUS_PENDING;
        $task->assigned_to = $attributes['assigned_to'] ?? $this->user->id;
        $task->created_by = $attributes['created_by'] ?? $this->user->id;
        $task->due_date = $attributes['due_date'] ?? null;
        $task->save(false);
        return $task;
    }

    /**
     * Testa criação de tarefa.
     */
    public function testCreateTask(): void
    {
        $task = $this->createTask(['title' => 'Implementar Login']);

        $this->assertNotNull($task->id);
        $this->assertEquals('Implementar Login', $task->title);
        $this->assertEquals(Task::STATUS_PENDING, $task->status);
        $this->assertEquals(Task::PRIORITY_MEDIUM, $task->priority);
    }

    /**
     * Testa marcação como concluída.
     */
    public function testMarkAsCompleted(): void
    {
        $task = $this->createTask();

        $this->assertFalse($task->isCompleted());
        $this->assertTrue($task->markAsCompleted());
        $this->assertTrue($task->isCompleted());
        $this->assertEquals(Task::STATUS_COMPLETED, $task->status);
        $this->assertNotNull($task->completed_at);
    }

    /**
     * Testa detecção de tarefa atrasada.
     */
    public function testIsOverdue(): void
    {
        // Sem data de vencimento = não atrasada
        $task1 = $this->createTask();
        $this->assertFalse($task1->isOverdue());

        // Data futura = não atrasada
        $task2 = $this->createTask([
            'title' => 'Futura',
            'due_date' => date('Y-m-d', strtotime('+7 days')),
        ]);
        $this->assertFalse($task2->isOverdue());

        // Data passada = atrasada
        $task3 = $this->createTask([
            'title' => 'Atrasada',
            'due_date' => date('Y-m-d', strtotime('-3 days')),
        ]);
        $this->assertTrue($task3->isOverdue());

        // Concluída com data passada = NÃO atrasada
        $task4 = $this->createTask([
            'title' => 'Concluída',
            'due_date' => date('Y-m-d', strtotime('-1 day')),
        ]);
        $task4->markAsCompleted();
        $this->assertFalse($task4->isOverdue());
    }

    /**
     * Testa labels de prioridade.
     */
    public function testPriorityLabel(): void
    {
        $task = $this->createTask(['priority' => Task::PRIORITY_HIGH]);
        $this->assertEquals('Alta', $task->getPriorityLabel());

        $task->priority = Task::PRIORITY_LOW;
        $this->assertEquals('Baixa', $task->getPriorityLabel());
    }

    /**
     * Testa labels de status.
     */
    public function testStatusLabel(): void
    {
        $task = $this->createTask(['status' => Task::STATUS_PENDING]);
        $this->assertEquals('Pendente', $task->getStatusLabel());

        $task->status = Task::STATUS_IN_PROGRESS;
        $this->assertEquals('Em Andamento', $task->getStatusLabel());

        $task->status = Task::STATUS_COMPLETED;
        $this->assertEquals('Concluída', $task->getStatusLabel());
    }

    /**
     * Testa cores de prioridade.
     */
    public function testPriorityColor(): void
    {
        $task = $this->createTask();

        $task->priority = Task::PRIORITY_HIGH;
        $this->assertEquals('red', $task->getPriorityColor());

        $task->priority = Task::PRIORITY_MEDIUM;
        $this->assertEquals('amber', $task->getPriorityColor());

        $task->priority = Task::PRIORITY_LOW;
        $this->assertEquals('emerald', $task->getPriorityColor());
    }

    /**
     * Testa cores de status.
     */
    public function testStatusColor(): void
    {
        $task = $this->createTask();

        $task->status = Task::STATUS_COMPLETED;
        $this->assertEquals('emerald', $task->getStatusColor());

        $task->status = Task::STATUS_IN_PROGRESS;
        $this->assertEquals('blue', $task->getStatusColor());

        $task->status = Task::STATUS_PENDING;
        $this->assertEquals('amber', $task->getStatusColor());
    }

    /**
     * Testa validações de campos obrigatórios.
     */
    public function testRequiredFields(): void
    {
        $task = new Task();
        $task->scenario = 'validation'; // Usar cenário com todas as validações
        $this->assertFalse($task->validate());

        $this->assertArrayHasKey('title', $task->errors);
        $this->assertArrayHasKey('project_id', $task->errors);
    }

    /**
     * Testa scope findPending.
     */
    public function testFindPending(): void
    {
        $this->createTask(['title' => 'Pendente 1', 'status' => Task::STATUS_PENDING]);
        $this->createTask(['title' => 'Em andamento', 'status' => Task::STATUS_IN_PROGRESS]);
        $this->createTask(['title' => 'Concluída', 'status' => Task::STATUS_COMPLETED]);

        $pending = Task::findPending()->all();
        $this->assertCount(2, $pending); // Pendente + Em andamento
    }

    /**
     * Testa scope findOverdue.
     */
    public function testFindOverdue(): void
    {
        $this->createTask([
            'title' => 'Atrasada 1',
            'due_date' => date('Y-m-d', strtotime('-2 days')),
        ]);
        $this->createTask([
            'title' => 'No prazo',
            'due_date' => date('Y-m-d', strtotime('+5 days')),
        ]);
        $this->createTask([
            'title' => 'Sem prazo',
        ]);

        $overdue = Task::findOverdue()->all();
        $this->assertCount(1, $overdue);
        $this->assertEquals('Atrasada 1', $overdue[0]->title);
    }

    /**
     * Testa relacionamento com projeto.
     */
    public function testProjectRelation(): void
    {
        $task = $this->createTask();
        $this->assertNotNull($task->project);
        $this->assertEquals($this->project->id, $task->project->id);
    }

    /**
     * Testa controle de acesso à tarefa.
     */
    public function testHasAccess(): void
    {
        $creator = $this->createTestUser(['email' => 'creator@dever.io']);
        $assignee = $this->createTestUser(['email' => 'assignee@dever.io']);
        $member = $this->createTestUser(['email' => 'member@dever.io']);
        $stranger = $this->createTestUser(['email' => 'stranger@dever.io']);

        // Adicionar membro ao projeto
        $this->project->addMember($member->id);

        // Criar tarefa
        $task = $this->createTask([
            'created_by' => $creator->id,
            'assigned_to' => $assignee->id,
        ]);

        // Criador deve ter acesso
        $this->assertTrue($task->hasAccess($creator->id));

        // Responsável deve ter acesso
        $this->assertTrue($task->hasAccess($assignee->id));

        // Membro do projeto deve ter acesso
        $this->assertTrue($task->hasAccess($member->id));

        // Dono do projeto deve ter acesso
        $this->assertTrue($task->hasAccess($this->user->id));

        // Usuário estranho não deve ter acesso
        $this->assertFalse($task->hasAccess($stranger->id));
    }

    /**
     * Testa segurança: usuário não pode acessar tarefa de outro projeto.
     */
    public function testSecurityUserCannotAccessOthersTasks(): void
    {
        $joao = $this->createTestUser(['email' => 'joao@dever.io']);
        $maria = $this->createTestUser(['email' => 'maria@dever.io']);

        $joaoProject = $this->createTestProject($joao, ['title' => 'Projeto João']);
        $mariaProject = $this->createTestProject($maria, ['title' => 'Projeto Maria']);

        $joaoTask = $this->createTask([
            'project_id' => $joaoProject->id,
            'created_by' => $joao->id,
            'assigned_to' => $joao->id,
            'title' => 'Tarefa João',
        ]);

        $mariaTask = $this->createTask([
            'project_id' => $mariaProject->id,
            'created_by' => $maria->id,
            'assigned_to' => $maria->id,
            'title' => 'Tarefa Maria',
        ]);

        // João não pode acessar tarefa de Maria
        $this->assertFalse($mariaTask->hasAccess($joao->id),
            'João não deveria ter acesso à tarefa de Maria');

        // Maria não pode acessar tarefa de João
        $this->assertFalse($joaoTask->hasAccess($maria->id),
            'Maria não deveria ter acesso à tarefa de João');

        // João pode acessar sua própria tarefa
        $this->assertTrue($joaoTask->hasAccess($joao->id),
            'João deveria ter acesso à sua própria tarefa');

        // Maria pode acessar sua própria tarefa
        $this->assertTrue($mariaTask->hasAccess($maria->id),
            'Maria deveria ter acesso à sua própria tarefa');
    }

    /**
     * Testa cenários de segurança para mass assignment.
     */
    public function testScenariosPreventMassAssignment(): void
    {
        $task = new Task();

        // Simular dados POST maliciosos tentando modificar campos protegidos
        $maliciousData = [
            'title' => 'Tarefa Segura',
            'description' => 'Descrição segura',
            'project_id' => 999, // Campo que não deveria ser modificável via POST
            'created_by' => 999, // Campo que não deveria ser modificável
            'created_at' => '2020-01-01', // Campo que não deveria ser modificável
        ];

        // Carregar dados usando cenário padrão (seguro)
        $task->scenario = Task::SCENARIO_DEFAULT;
        $task->load($maliciousData, '');

        // Verificar que apenas campos seguros foram carregados
        $this->assertEquals('Tarefa Segura', $task->title);
        $this->assertEquals('Descrição segura', $task->description);

        // Verificar que campos protegidos não foram modificados
        $this->assertNull($task->project_id);
        $this->assertNull($task->created_by);
        $this->assertNull($task->created_at);
    }
}
