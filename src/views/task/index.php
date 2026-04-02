<?php
/**
 * View: Listagem de Tarefas do Projeto com filtros
 *
 * @var yii\web\View $this
 * @var app\models\Project $project
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var string|null $filterStatus
 * @var string|null $filterPriority
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
?>

<!-- Breadcrumb -->
<div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
    <a href="<?= Url::to(['/project/index']) ?>" class="hover:text-primary-600 transition-colors">Projetos</a>
    <i data-lucide="chevron-right" class="w-4 h-4"></i>
    <a href="<?= Url::to(['/project/view', 'id' => $project->id]) ?>" class="hover:text-primary-600 transition-colors"><?= Html::encode($project->title) ?></a>
    <i data-lucide="chevron-right" class="w-4 h-4"></i>
    <span class="text-slate-800">Tarefas</span>
</div>

<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Tarefas</h2>
        <p class="text-sm text-slate-500 mt-1"><?= $dataProvider->getTotalCount() ?> tarefas encontradas</p>
    </div>
    <a href="<?= Url::to(['/task/create', 'projectId' => $project->id]) ?>"
       class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-all">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Nova Tarefa
    </a>
</div>

<!-- Filtros -->
<div class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
    <form method="get" action="<?= Url::to(['/task/index', 'projectId' => $project->id]) ?>" class="flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-slate-600">Status:</label>
            <select name="status" class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                <option value="">Todos</option>
                <option value="pending" <?= $filterStatus === 'pending' ? 'selected' : '' ?>>Pendente</option>
                <option value="in_progress" <?= $filterStatus === 'in_progress' ? 'selected' : '' ?>>Em Andamento</option>
                <option value="completed" <?= $filterStatus === 'completed' ? 'selected' : '' ?>>Concluída</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-slate-600">Prioridade:</label>
            <select name="priority" class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                <option value="">Todas</option>
                <option value="low" <?= $filterPriority === 'low' ? 'selected' : '' ?>>Baixa</option>
                <option value="medium" <?= $filterPriority === 'medium' ? 'selected' : '' ?>>Média</option>
                <option value="high" <?= $filterPriority === 'high' ? 'selected' : '' ?>>Alta</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors">
            Filtrar
        </button>
        <?php if ($filterStatus || $filterPriority): ?>
            <a href="<?= Url::to(['/task/index', 'projectId' => $project->id]) ?>" class="text-sm text-primary-600 hover:text-primary-700">
                Limpar filtros
            </a>
        <?php endif; ?>
    </form>
</div>

<!-- Lista de Tarefas -->
<div class="bg-white rounded-xl border border-slate-200">
    <div class="divide-y divide-slate-100">
        <?php if ($dataProvider->getCount() === 0): ?>
            <div class="p-12 text-center">
                <i data-lucide="clipboard-list" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <p class="text-slate-400">Nenhuma tarefa encontrada</p>
            </div>
        <?php else: ?>
            <?php foreach ($dataProvider->getModels() as $task): ?>
                <div class="px-5 py-4 hover:bg-slate-50 transition-colors flex items-center gap-4">
                    <!-- Checkbox -->
                    <?php if (!$task->isCompleted()): ?>
                        <form method="post" action="<?= Url::to(['/task/complete', 'projectId' => $project->id, 'id' => $task->id]) ?>">
                            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                            <button type="submit" class="w-5 h-5 border-2 border-slate-300 rounded hover:border-emerald-500 hover:bg-emerald-50 transition-colors" title="Concluir"></button>
                        </form>
                    <?php else: ?>
                        <div class="w-5 h-5 bg-emerald-500 rounded flex items-center justify-center">
                            <i data-lucide="check" class="w-3 h-3 text-white"></i>
                        </div>
                    <?php endif; ?>

                    <!-- Info -->
                    <a href="<?= Url::to(['/task/view', 'projectId' => $project->id, 'id' => $task->id]) ?>" class="flex-1 min-w-0">
                        <p class="text-sm font-medium <?= $task->isCompleted() ? 'text-slate-400 line-through' : 'text-slate-800' ?> truncate">
                            <?= Html::encode($task->title) ?>
                        </p>
                        <?php if ($task->assignee): ?>
                            <p class="text-xs text-slate-400 mt-0.5">Responsável: <?= Html::encode($task->assignee->name) ?></p>
                        <?php endif; ?>
                    </a>

                    <!-- Badges -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <?php if ($task->isOverdue()): ?>
                            <span class="text-xs text-red-500 font-bold">⚠ Atrasada</span>
                        <?php endif; ?>
                        <?php if ($task->due_date): ?>
                            <span class="text-xs text-slate-400"><?= date('d/m/Y', strtotime($task->due_date)) ?></span>
                        <?php endif; ?>
                        <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-<?= $task->getPriorityColor() ?>-100 text-<?= $task->getPriorityColor() ?>-700">
                            <?= Html::encode($task->getPriorityLabel()) ?>
                        </span>
                        <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-<?= $task->getStatusColor() ?>-100 text-<?= $task->getStatusColor() ?>-700">
                            <?= Html::encode($task->getStatusLabel()) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Paginação -->
<div class="mt-6 flex justify-center">
    <?= LinkPager::widget([
        'pagination' => $dataProvider->pagination,
        'options' => ['class' => 'flex items-center gap-1'],
        'linkOptions' => ['class' => 'px-3 py-1.5 text-sm rounded-lg border border-slate-200 hover:bg-primary-50 hover:text-primary-600 transition-colors'],
        'activePageCssClass' => 'bg-primary-600 text-white border-primary-600',
    ]) ?>
</div>
