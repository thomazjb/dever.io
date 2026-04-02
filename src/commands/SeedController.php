<?php
/**
 * SeedController - Comando console para popular banco com dados de exemplo
 *
 * Uso: php yii seed
 *
 * Cria usuários, projetos, membros e tarefas de demonstração
 * para facilitar o teste e desenvolvimento da aplicação.
 *
 * @package app\commands
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\User;
use app\models\Project;
use app\models\ProjectUser;
use app\models\Task;

class SeedController extends Controller
{
    /**
     * Popula o banco de dados com dados de demonstração.
     *
     * @return int Exit code
     */
    public function actionIndex(): int
    {
        $this->stdout("\n🌱 Populando banco de dados com dados de demonstração...\n\n");

        // =============================================
        // 1. Criar Usuários
        // =============================================
        $this->stdout("👤 Criando usuários...\n");

        $users = [];
        $usersData = [
            ['name' => 'Admin Dever.io', 'email' => 'admin@dever.io', 'password' => 'admin123'],
            ['name' => 'João Silva', 'email' => 'joao@dever.io', 'password' => 'senha123'],
            ['name' => 'Maria Santos', 'email' => 'maria@dever.io', 'password' => 'senha123'],
            ['name' => 'Pedro Oliveira', 'email' => 'pedro@dever.io', 'password' => 'senha123'],
        ];

        foreach ($usersData as $data) {
            $existing = User::findByEmail($data['email']);
            if ($existing) {
                $users[] = $existing;
                $this->stdout("  ⏩ {$data['email']} (já existe)\n");
                continue;
            }

            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->setPassword($data['password']);
            $user->generateAuthKey();
            $user->status = User::STATUS_ACTIVE;

            if ($user->save()) {
                $users[] = $user;
                $this->stdout("  ✅ {$data['email']}\n");
            } else {
                $this->stderr("  ❌ Erro ao criar {$data['email']}: " . implode(', ', $user->getFirstErrors()) . "\n");
            }
        }

        if (count($users) < 2) {
            $this->stderr("\n❌ Necessário pelo menos 2 usuários para continuar.\n");
            return ExitCode::DATAERR;
        }

        // =============================================
        // 2. Criar Projetos
        // =============================================
        $this->stdout("\n📁 Criando projetos...\n");

        $projectsData = [
            [
                'title' => 'Landing Page Dever.io',
                'description' => 'Desenvolvimento da página de apresentação do produto com seções de features, pricing e FAQ.',
                'owner' => 0,
                'start_date' => date('Y-m-d', strtotime('-30 days')),
                'end_date' => date('Y-m-d', strtotime('+30 days')),
            ],
            [
                'title' => 'API REST - Backend',
                'description' => 'Implementação completa da API RESTful com autenticação JWT, CRUD de recursos e documentação Swagger.',
                'owner' => 0,
                'start_date' => date('Y-m-d', strtotime('-15 days')),
                'end_date' => date('Y-m-d', strtotime('+60 days')),
            ],
            [
                'title' => 'App Mobile React Native',
                'description' => 'Aplicativo mobile cross-platform com React Native, integração com API e notificações push.',
                'owner' => 1,
                'start_date' => date('Y-m-d', strtotime('-5 days')),
                'end_date' => date('Y-m-d', strtotime('+90 days')),
            ],
        ];

        $projects = [];
        foreach ($projectsData as $data) {
            $ownerIndex = min($data['owner'], count($users) - 1);
            $project = new Project();
            $project->title = $data['title'];
            $project->description = $data['description'];
            $project->owner_id = $users[$ownerIndex]->id;
            $project->start_date = $data['start_date'];
            $project->end_date = $data['end_date'];

            if ($project->save()) {
                $projects[] = $project;
                $this->stdout("  ✅ {$data['title']}\n");
            } else {
                $this->stderr("  ❌ Erro: " . implode(', ', $project->getFirstErrors()) . "\n");
            }
        }

        // =============================================
        // 3. Adicionar Membros
        // =============================================
        $this->stdout("\n👥 Adicionando membros aos projetos...\n");

        // Adicionar membros extras a cada projeto
        foreach ($projects as $i => $project) {
            foreach ($users as $j => $user) {
                if ($user->id !== $project->owner_id) {
                    $project->addMember($user->id);
                    $this->stdout("  ✅ {$user->name} → {$project->title}\n");
                }
            }
        }

        // =============================================
        // 4. Criar Tarefas
        // =============================================
        $this->stdout("\n📝 Criando tarefas...\n");

        $tasksData = [
            // Projeto 1 - Landing Page
            ['project' => 0, 'title' => 'Design do hero section', 'priority' => 'high', 'status' => 'completed', 'due' => '-10 days'],
            ['project' => 0, 'title' => 'Implementar navbar responsiva', 'priority' => 'high', 'status' => 'completed', 'due' => '-8 days'],
            ['project' => 0, 'title' => 'Seção de features com ícones', 'priority' => 'medium', 'status' => 'in_progress', 'due' => '+3 days'],
            ['project' => 0, 'title' => 'Tabela de preços', 'priority' => 'medium', 'status' => 'pending', 'due' => '+7 days'],
            ['project' => 0, 'title' => 'Formulário de contato', 'priority' => 'low', 'status' => 'pending', 'due' => '+14 days'],
            ['project' => 0, 'title' => 'SEO e meta tags', 'priority' => 'low', 'status' => 'pending', 'due' => '+20 days'],
            ['project' => 0, 'title' => 'Correção de bugs no mobile', 'priority' => 'high', 'status' => 'pending', 'due' => '-2 days'], // Atrasada!

            // Projeto 2 - API REST
            ['project' => 1, 'title' => 'Setup do projeto Laravel', 'priority' => 'high', 'status' => 'completed', 'due' => '-12 days'],
            ['project' => 1, 'title' => 'Migração do banco de dados', 'priority' => 'high', 'status' => 'completed', 'due' => '-10 days'],
            ['project' => 1, 'title' => 'Implementar autenticação JWT', 'priority' => 'high', 'status' => 'in_progress', 'due' => '+2 days'],
            ['project' => 1, 'title' => 'CRUD de usuários', 'priority' => 'medium', 'status' => 'pending', 'due' => '+10 days'],
            ['project' => 1, 'title' => 'CRUD de produtos', 'priority' => 'medium', 'status' => 'pending', 'due' => '+15 days'],
            ['project' => 1, 'title' => 'Upload de arquivos S3', 'priority' => 'medium', 'status' => 'pending', 'due' => '+20 days'],
            ['project' => 1, 'title' => 'Documentação Swagger', 'priority' => 'low', 'status' => 'pending', 'due' => '+30 days'],
            ['project' => 1, 'title' => 'Testes automatizados', 'priority' => 'high', 'status' => 'pending', 'due' => '-1 day'], // Atrasada!

            // Projeto 3 - App Mobile
            ['project' => 2, 'title' => 'Configurar projeto React Native', 'priority' => 'high', 'status' => 'completed', 'due' => '-3 days'],
            ['project' => 2, 'title' => 'Tela de login e registro', 'priority' => 'high', 'status' => 'in_progress', 'due' => '+5 days'],
            ['project' => 2, 'title' => 'Navegação com React Navigation', 'priority' => 'medium', 'status' => 'pending', 'due' => '+10 days'],
            ['project' => 2, 'title' => 'Integração com API', 'priority' => 'high', 'status' => 'pending', 'due' => '+20 days'],
            ['project' => 2, 'title' => 'Notificações push', 'priority' => 'medium', 'status' => 'pending', 'due' => '+40 days'],
        ];

        foreach ($tasksData as $data) {
            $projectIndex = min($data['project'], count($projects) - 1);
            $project = $projects[$projectIndex];
            $assigneeIndex = array_rand($users);

            $task = new Task();
            $task->project_id = $project->id;
            $task->title = $data['title'];
            $task->description = "Descrição detalhada da tarefa: {$data['title']}";
            $task->priority = $data['priority'];
            $task->status = $data['status'];
            $task->due_date = date('Y-m-d', strtotime($data['due']));
            $task->assigned_to = $users[$assigneeIndex]->id;
            $task->created_by = $project->owner_id;

            if ($data['status'] === 'completed') {
                $task->completed_at = time() - rand(86400, 864000);
            }

            if ($task->save()) {
                $statusIcon = match ($data['status']) {
                    'completed' => '✅',
                    'in_progress' => '🔄',
                    default => '⏳',
                };
                $this->stdout("  {$statusIcon} {$data['title']}\n");
            } else {
                $this->stderr("  ❌ Erro: " . implode(', ', $task->getFirstErrors()) . "\n");
            }
        }

        // =============================================
        // Resumo
        // =============================================
        $this->stdout("\n" . str_repeat('═', 50) . "\n");
        $this->stdout("🎉 Seed concluído com sucesso!\n\n");
        $this->stdout("  👤 Usuários criados: " . count($users) . "\n");
        $this->stdout("  📁 Projetos criados: " . count($projects) . "\n");
        $this->stdout("  📝 Tarefas criadas:  " . count($tasksData) . "\n");
        $this->stdout("\n  🔑 Login de teste:\n");
        $this->stdout("     Email: admin@dever.io\n");
        $this->stdout("     Senha: admin123\n");
        $this->stdout(str_repeat('═', 50) . "\n\n");

        return ExitCode::OK;
    }

    /**
     * Limpa todos os dados do banco.
     */
    public function actionClear(): int
    {
        if (!$this->confirm('⚠️  Tem certeza que deseja excluir TODOS os dados?')) {
            return ExitCode::OK;
        }

        $db = Yii::$app->db;

        $this->stdout("\n🗑️  Limpando dados...\n");

        $db->createCommand('SET FOREIGN_KEY_CHECKS = 0')->execute();
        $db->createCommand('TRUNCATE TABLE {{%attachment}}')->execute();
        $db->createCommand('TRUNCATE TABLE {{%task}}')->execute();
        $db->createCommand('TRUNCATE TABLE {{%project_user}}')->execute();
        $db->createCommand('TRUNCATE TABLE {{%project}}')->execute();
        $db->createCommand('TRUNCATE TABLE {{%user}}')->execute();
        $db->createCommand('SET FOREIGN_KEY_CHECKS = 1')->execute();

        $this->stdout("✅ Todos os dados foram removidos.\n\n");

        return ExitCode::OK;
    }
}
