<?php
/**
 * View: Minhas Tarefas (todas as tarefas atribuídas ao usuário)
 *
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var string|null $filterStatus
 * @var string|null $filterPriority
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
?>

<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Minhas Tarefas</h2>
        <p class="text-sm text-slate-500 mt-1"><?= $dataProvider->getTotalCount() ?> tarefas atribuídas a você</p>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
    <form method="get" action="<?= Url::to(['/task/my-tasks']) ?>" class="flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-slate-600">Status:</label>
            <select name="status" class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                <option value="">Todos</option>
                <option value="pending" <?= $filterStatus === 'pending' ? 'selected' : '' ?>>Pendente</option>
                <option value="in_progress" <?= $filterStatus === 'in_progress' ? 'selected' : '' ?>>Em Andamento</option>
                <option value="completed" <?= $filterStatus === 'completed' ? 'selected' : '' ?>>Concluída</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-slate-600">Prioridade:</label>
            <select name="priority" class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                <option value="">Todas</option>
                <option value="low" <?= $filterPriority === 'low' ? 'selected' : '' ?>>Baixa</option>
                <option value="medium" <?= $filterPriority === 'medium' ? 'selected' : '' ?>>Média</option>
                <option value="high" <?= $filterPriority === 'high' ? 'selected' : '' ?>>Alta</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors">Filtrar</button>
        <?php if ($filterStatus || $filterPriority): ?>
            <a href="<?= Url::to(['/task/my-tasks']) ?>" class="text-sm text-primary-600 hover:text-primary-700">Limpar</a>
        <?php endif; ?>
    </form>
</div>

<!-- Lista -->
<div class="bg-white rounded-xl border border-slate-200">
    <div class="divide-y divide-slate-100">
        <?php if ($dataProvider->getCount() === 0): ?>
            <div class="p-12 text-center">
                <i data-lucide="check-circle" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <p class="text-slate-400">Nenhuma tarefa encontrada</p>
            </div>
        <?php else: ?>
            <?php foreach ($dataProvider->getModels() as $task): ?>
                <div class="px-5 py-4 hover:bg-slate-50 transition-colors flex items-center gap-4">
                    <?php if (!$task->isCompleted()): ?>
                        <form method="post" action="<?= Url::to(['/task/complete', 'projectId' => $task->project_id, 'id' => $task->id]) ?>">
                            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                            <button type="submit" class="w-5 h-5 border-2 border-slate-300 rounded hover:border-emerald-500 hover:bg-emerald-50 transition-colors" title="Concluir"></button>
                        </form>
                    <?php else: ?>
                        <div class="w-5 h-5 bg-emerald-500 rounded flex items-center justify-center">
                            <i data-lucide="check" class="w-3 h-3 text-white"></i>
                        </div>
                    <?php endif; ?>

                    <a href="<?= Url::to(['/task/view', 'projectId' => $task->project_id, 'id' => $task->id]) ?>" class="flex-1 min-w-0">
                        <p class="text-sm font-medium <?= $task->isCompleted() ? 'text-slate-400 line-through' : 'text-slate-800' ?> truncate">
                            <?= Html::encode($task->title) ?>
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            <?= Html::encode($task->project->title ?? '') ?>
                        </p>
                    </a>

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

<div class="mt-6 flex justify-center">
    <?= LinkPager::widget([
        'pagination' => $dataProvider->pagination,
        'options' => ['class' => 'flex items-center gap-1'],
        'linkOptions' => ['class' => 'px-3 py-1.5 text-sm rounded-lg border border-slate-200 hover:bg-primary-50 hover:text-primary-600 transition-colors'],
        'activePageCssClass' => 'bg-primary-600 text-white border-primary-600',
    ]) ?>
</div>
