<?php
/**
 * View: Detalhes do Projeto
 *
 * @var yii\web\View $this
 * @var app\models\Project $project
 * @var yii\data\ActiveDataProvider $taskDataProvider
 */

use yii\helpers\Html;
use yii\helpers\Url;
?>

<!-- Breadcrumb -->
<div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
    <a href="<?= Url::to(['/project/index']) ?>" class="hover:text-primary-600 transition-colors">Projetos</a>
    <i data-lucide="chevron-right" class="w-4 h-4"></i>
    <span class="text-slate-800"><?= Html::encode($project->title) ?></span>
</div>

<!-- Header do Projeto -->
<div class="bg-white rounded-xl border border-slate-200 p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div class="flex-1">
            <h2 class="text-2xl font-bold text-slate-800 mb-2"><?= Html::encode($project->title) ?></h2>
            <?php if ($project->description): ?>
                <p class="text-slate-600 text-sm leading-relaxed mb-4"><?= Html::encode($project->description) ?></p>
            <?php endif; ?>

            <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500">
                <?php if ($project->start_date): ?>
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        Início: <?= date('d/m/Y', strtotime($project->start_date)) ?>
                    </span>
                <?php endif; ?>
                <?php if ($project->end_date): ?>
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="calendar-check" class="w-4 h-4"></i>
                        Previsão: <?= date('d/m/Y', strtotime($project->end_date)) ?>
                    </span>
                <?php endif; ?>
                <span class="flex items-center gap-1.5">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    Dono: <?= Html::encode($project->owner->name ?? '') ?>
                </span>
            </div>
        </div>

        <!-- Ações -->
        <?php if ($project->isOwner()): ?>
            <div class="flex items-center gap-2">
                <a href="<?= Url::to(['/project/members', 'id' => $project->id]) ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    Membros
                </a>
                <a href="<?= Url::to(['/project/update', 'id' => $project->id]) ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                    <i data-lucide="pencil" class="w-4 h-4"></i>
                    Editar
                </a>
                <form method="post" action="<?= Url::to(['/project/delete', 'id' => $project->id]) ?>" class="inline">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors"
                            onclick="return confirm('Tem certeza que deseja excluir este projeto? Todas as tarefas serão removidas.')">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        Excluir
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- Barra de progresso -->
    <?php $progress = $project->getProgress(); $totalTasks = $project->getTaskCount(); ?>
    <div class="mt-5 pt-5 border-t border-slate-100">
        <div class="flex items-center justify-between text-sm mb-2">
            <span class="text-slate-500"><?= $totalTasks ?> tarefas no total</span>
            <span class="font-semibold text-primary-600"><?= $progress ?>% concluído</span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-2">
            <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-2 rounded-full transition-all duration-500"
                 style="width: <?= $progress ?>%"></div>
        </div>
    </div>
</div>

<!-- Anexos do Projeto -->
<?php $attachments = $project->attachments; ?>
<?php if (!empty($attachments)): ?>
<div class="bg-white rounded-xl border border-slate-200 p-5 mb-6">
    <h3 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
        <i data-lucide="paperclip" class="w-4 h-4"></i>
        Anexos (<?= count($attachments) ?>)
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        <?php foreach ($attachments as $att): ?>
            <div class="flex items-center justify-between bg-slate-50 rounded-lg px-4 py-2.5">
                <a href="<?= Html::encode($att->getDownloadUrl()) ?>" target="_blank"
                   class="flex items-center gap-3 text-sm text-slate-700 hover:text-primary-600 transition-colors">
                    <i data-lucide="<?= $att->isImage() ? 'image' : 'file-text' ?>" class="w-4 h-4 text-slate-500"></i>
                    <span class="truncate"><?= Html::encode($att->original_name) ?></span>
                    <span class="text-xs text-slate-400"><?= $att->getFormattedSize() ?></span>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Tarefas do Projeto -->
<div class="bg-white rounded-xl border border-slate-200">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-semibold text-slate-800 flex items-center gap-2">
            <i data-lucide="list-todo" class="w-4 h-4"></i>
            Tarefas
        </h3>
        <a href="<?= Url::to(['/task/create', 'projectId' => $project->id]) ?>"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nova Tarefa
        </a>
    </div>

    <!-- Filtros -->
    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50 flex flex-wrap items-center gap-2">
        <a href="<?= Url::to(['/task/index', 'projectId' => $project->id]) ?>"
           class="px-3 py-1 text-xs font-medium rounded-full transition-colors bg-slate-200 text-slate-700 hover:bg-primary-100 hover:text-primary-700">
            Todas
        </a>
        <a href="<?= Url::to(['/task/index', 'projectId' => $project->id, 'status' => 'pending']) ?>"
           class="px-3 py-1 text-xs font-medium rounded-full transition-colors bg-amber-100 text-amber-700 hover:bg-amber-200">
            Pendentes
        </a>
        <a href="<?= Url::to(['/task/index', 'projectId' => $project->id, 'status' => 'in_progress']) ?>"
           class="px-3 py-1 text-xs font-medium rounded-full transition-colors bg-blue-100 text-blue-700 hover:bg-blue-200">
            Em Andamento
        </a>
        <a href="<?= Url::to(['/task/index', 'projectId' => $project->id, 'status' => 'completed']) ?>"
           class="px-3 py-1 text-xs font-medium rounded-full transition-colors bg-emerald-100 text-emerald-700 hover:bg-emerald-200">
            Concluídas
        </a>
    </div>

    <!-- Lista de Tarefas -->
    <div class="divide-y divide-slate-100">
        <?php if ($taskDataProvider->getCount() === 0): ?>
            <div class="p-8 text-center">
                <i data-lucide="clipboard-list" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                <p class="text-slate-400 text-sm">Nenhuma tarefa neste projeto</p>
            </div>
        <?php else: ?>
            <?php foreach ($taskDataProvider->getModels() as $task): ?>
                <div class="px-5 py-3.5 hover:bg-slate-50 transition-colors flex items-center gap-4">
                    <!-- Checkbox de conclusão -->
                    <?php if (!$task->isCompleted()): ?>
                        <form method="post" action="<?= Url::to(['/task/complete', 'projectId' => $project->id, 'id' => $task->id]) ?>" class="inline">
                            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                            <button type="submit" class="w-5 h-5 border-2 border-slate-300 rounded hover:border-emerald-500 hover:bg-emerald-50 transition-colors flex items-center justify-center"
                                    title="Marcar como concluída">
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="w-5 h-5 bg-emerald-500 rounded flex items-center justify-center">
                            <i data-lucide="check" class="w-3 h-3 text-white"></i>
                        </div>
                    <?php endif; ?>

                    <!-- Info da Tarefa -->
                    <a href="<?= Url::to(['/task/view', 'projectId' => $project->id, 'id' => $task->id]) ?>"
                       class="flex-1 min-w-0">
                        <p class="text-sm font-medium <?= $task->isCompleted() ? 'text-slate-400 line-through' : 'text-slate-800' ?> truncate">
                            <?= Html::encode($task->title) ?>
                        </p>
                    </a>

                    <!-- Badges -->
                    <div class="flex items-center gap-2">
                        <?php if ($task->isOverdue()): ?>
                            <span class="text-xs text-red-500 font-medium">Atrasada</span>
                        <?php endif; ?>
                        <?php if ($task->due_date): ?>
                            <span class="text-xs text-slate-400"><?= date('d/m', strtotime($task->due_date)) ?></span>
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
