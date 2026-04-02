<?php
/**
 * View: Dashboard principal
 *
 * Exibe resumo com contadores, tarefas atrasadas,
 * tarefas próximas do vencimento e projetos recentes.
 *
 * @var yii\web\View $this
 * @var int $totalTasks
 * @var int $pendingCount
 * @var int $inProgressCount
 * @var int $completedCount
 * @var int $overdueCount
 * @var yii\data\ActiveDataProvider $overdueTasks
 * @var yii\data\ActiveDataProvider $upcomingTasks
 * @var app\models\Project[] $projects
 * @var int $projectCount
 */

use yii\helpers\Html;
use yii\helpers\Url;
?>

<!-- Cabeçalho -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800">Olá, <?= Html::encode(Yii::$app->user->identity->name) ?>! 👋</h2>
    <p class="text-slate-500 mt-1">Aqui está o resumo das suas atividades</p>
</div>

<!-- Cards de Estatísticas -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <!-- Total de Projetos -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                <i data-lucide="folder-kanban" class="w-5 h-5 text-primary-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800"><?= $projectCount ?></p>
                <p class="text-xs text-slate-500">Projetos</p>
            </div>
        </div>
    </div>

    <!-- Tarefas Pendentes -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                <i data-lucide="clock" class="w-5 h-5 text-amber-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800"><?= $pendingCount ?></p>
                <p class="text-xs text-slate-500">Pendentes</p>
            </div>
        </div>
    </div>

    <!-- Em Andamento -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i data-lucide="loader" class="w-5 h-5 text-blue-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800"><?= $inProgressCount ?></p>
                <p class="text-xs text-slate-500">Em Andamento</p>
            </div>
        </div>
    </div>

    <!-- Concluídas -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800"><?= $completedCount ?></p>
                <p class="text-xs text-slate-500">Concluídas</p>
            </div>
        </div>
    </div>

    <!-- Atrasadas -->
    <div class="bg-white rounded-xl border border-red-200 p-5 hover:shadow-md transition-shadow <?= $overdueCount > 0 ? 'ring-2 ring-red-100' : '' ?>">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-red-600"><?= $overdueCount ?></p>
                <p class="text-xs text-slate-500">Atrasadas</p>
            </div>
        </div>
    </div>
</div>

<!-- Barra de Progresso Geral -->
<?php if ($totalTasks > 0): ?>
<div class="bg-white rounded-xl border border-slate-200 p-5 mb-8">
    <div class="flex items-center justify-between mb-2">
        <p class="text-sm font-medium text-slate-700">Progresso Geral</p>
        <p class="text-sm font-bold text-primary-600"><?= $totalTasks > 0 ? round(($completedCount / $totalTasks) * 100) : 0 ?>%</p>
    </div>
    <div class="w-full bg-slate-200 rounded-full h-2.5">
        <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-2.5 rounded-full transition-all duration-500"
             style="width: <?= $totalTasks > 0 ? round(($completedCount / $totalTasks) * 100) : 0 ?>%"></div>
    </div>
    <p class="text-xs text-slate-400 mt-2"><?= $completedCount ?> de <?= $totalTasks ?> tarefas concluídas</p>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Tarefas Atrasadas -->
    <div class="bg-white rounded-xl border border-slate-200">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500"></i>
                Tarefas Atrasadas
            </h3>
            <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-full"><?= $overdueCount ?></span>
        </div>
        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
            <?php if ($overdueTasks->getCount() === 0): ?>
                <div class="p-5 text-center">
                    <i data-lucide="party-popper" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                    <p class="text-sm text-slate-400">Nenhuma tarefa atrasada! 🎉</p>
                </div>
            <?php else: ?>
                <?php foreach ($overdueTasks->getModels() as $task): ?>
                    <a href="<?= Url::to(['/task/view', 'projectId' => $task->project_id, 'id' => $task->id]) ?>"
                       class="block px-5 py-3 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-slate-800 truncate"><?= Html::encode($task->title) ?></p>
                                <p class="text-xs text-slate-400 mt-0.5"><?= Html::encode($task->project->title ?? '') ?></p>
                            </div>
                            <div class="ml-3 flex items-center gap-2">
                                <span class="text-xs text-red-500 font-medium whitespace-nowrap">
                                    <?= date('d/m', strtotime($task->due_date)) ?>
                                </span>
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-<?= $task->getPriorityColor() ?>-100 text-<?= $task->getPriorityColor() ?>-700">
                                    <?= Html::encode($task->getPriorityLabel()) ?>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Próximas Tarefas -->
    <div class="bg-white rounded-xl border border-slate-200">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i>
                Próximas Tarefas
            </h3>
        </div>
        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
            <?php if ($upcomingTasks->getCount() === 0): ?>
                <div class="p-5 text-center">
                    <i data-lucide="inbox" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                    <p class="text-sm text-slate-400">Nenhuma tarefa futura</p>
                </div>
            <?php else: ?>
                <?php foreach ($upcomingTasks->getModels() as $task): ?>
                    <a href="<?= Url::to(['/task/view', 'projectId' => $task->project_id, 'id' => $task->id]) ?>"
                       class="block px-5 py-3 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-slate-800 truncate"><?= Html::encode($task->title) ?></p>
                                <p class="text-xs text-slate-400 mt-0.5"><?= Html::encode($task->project->title ?? '') ?></p>
                            </div>
                            <div class="ml-3 flex items-center gap-2">
                                <span class="text-xs text-slate-500 font-medium whitespace-nowrap">
                                    <?= date('d/m', strtotime($task->due_date)) ?>
                                </span>
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-<?= $task->getStatusColor() ?>-100 text-<?= $task->getStatusColor() ?>-700">
                                    <?= Html::encode($task->getStatusLabel()) ?>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Projetos Recentes -->
<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-slate-800">Projetos Recentes</h3>
        <a href="<?= Url::to(['/project/index']) ?>" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
            Ver todos →
        </a>
    </div>

    <?php if (empty($projects)): ?>
        <div class="bg-white rounded-xl border border-slate-200 p-8 text-center">
            <i data-lucide="folder-plus" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
            <p class="text-slate-500 mb-4">Nenhum projeto ainda. Crie seu primeiro projeto!</p>
            <a href="<?= Url::to(['/project/create']) ?>"
               class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Novo Projeto
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($projects as $project): ?>
                <a href="<?= Url::to(['/project/view', 'id' => $project->id]) ?>"
                   class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-md hover:border-primary-200 transition-all group">
                    <div class="flex items-start justify-between mb-3">
                        <h4 class="font-semibold text-slate-800 group-hover:text-primary-600 transition-colors truncate"><?= Html::encode($project->title) ?></h4>
                        <?php if ($project->isOwner()): ?>
                            <span class="text-xs bg-primary-100 text-primary-700 px-2 py-0.5 rounded-full whitespace-nowrap ml-2">Dono</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($project->description): ?>
                        <p class="text-sm text-slate-500 line-clamp-2 mb-3"><?= Html::encode(mb_substr($project->description, 0, 100)) ?></p>
                    <?php endif; ?>
                    <!-- Barra de progresso do projeto -->
                    <?php $progress = $project->getProgress(); ?>
                    <div class="mt-auto">
                        <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
                            <span><?= $project->getTaskCount() ?> tarefas</span>
                            <span><?= $progress ?>%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5">
                            <div class="bg-primary-500 h-1.5 rounded-full" style="width: <?= $progress ?>%"></div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
