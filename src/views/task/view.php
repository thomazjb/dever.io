<?php
/**
 * View: Detalhes da Tarefa
 *
 * @var yii\web\View $this
 * @var app\models\Project $project
 * @var app\models\Task $task
 */

use yii\helpers\Html;
use yii\helpers\Url;
?>

<!-- Breadcrumb -->
<div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
    <a href="<?= Url::to(['/project/index']) ?>" class="hover:text-primary-600 transition-colors">Projetos</a>
    <i data-lucide="chevron-right" class="w-4 h-4"></i>
    <a href="<?= Url::to(['/project/view', 'id' => $project->id]) ?>" class="hover:text-primary-600 transition-colors"><?= Html::encode($project->title) ?></a>
    <i data-lucide="chevron-right" class="w-4 h-4"></i>
    <span class="text-slate-800"><?= Html::encode($task->title) ?></span>
</div>

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <!-- Header -->
        <div class="p-6 border-b border-slate-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-xl font-bold <?= $task->isCompleted() ? 'text-slate-400 line-through' : 'text-slate-800' ?>">
                            <?= Html::encode($task->title) ?>
                        </h2>
                        <?php if ($task->isOverdue()): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-bold rounded-full bg-red-100 text-red-700">
                                <i data-lucide="alert-triangle" class="w-3 h-3"></i> Atrasada
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-<?= $task->getStatusColor() ?>-100 text-<?= $task->getStatusColor() ?>-700">
                            <?= Html::encode($task->getStatusLabel()) ?>
                        </span>
                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-<?= $task->getPriorityColor() ?>-100 text-<?= $task->getPriorityColor() ?>-700">
                            <?= Html::encode($task->getPriorityLabel()) ?>
                        </span>
                    </div>
                </div>

                <!-- Ações -->
                <div class="flex items-center gap-2">
                    <?php if (!$task->isCompleted()): ?>
                        <form method="post" action="<?= Url::to(['/task/complete', 'projectId' => $project->id, 'id' => $task->id]) ?>" class="inline">
                            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                Concluir
                            </button>
                        </form>
                    <?php endif; ?>
                    <a href="<?= Url::to(['/task/update', 'projectId' => $project->id, 'id' => $task->id]) ?>"
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                        <i data-lucide="pencil" class="w-4 h-4"></i>
                        Editar
                    </a>
                    <form method="post" action="<?= Url::to(['/task/delete', 'projectId' => $project->id, 'id' => $task->id]) ?>" class="inline">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors"
                                onclick="return confirm('Excluir esta tarefa?')">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Detalhes -->
        <div class="p-6 space-y-5">
            <!-- Descrição -->
            <?php if ($task->description): ?>
                <div>
                    <h4 class="text-sm font-medium text-slate-500 mb-2">Descrição</h4>
                    <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line"><?= Html::encode($task->description) ?></p>
                </div>
            <?php endif; ?>

            <!-- Grid de informações -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-slate-50 rounded-lg p-3">
                    <p class="text-xs text-slate-400 mb-1">Responsável</p>
                    <p class="text-sm font-medium text-slate-700"><?= Html::encode($task->assignee->name ?? 'Não atribuído') ?></p>
                </div>
                <div class="bg-slate-50 rounded-lg p-3">
                    <p class="text-xs text-slate-400 mb-1">Vencimento</p>
                    <p class="text-sm font-medium <?= $task->isOverdue() ? 'text-red-600' : 'text-slate-700' ?>">
                        <?= $task->due_date ? date('d/m/Y', strtotime($task->due_date)) : '—' ?>
                    </p>
                </div>
                <div class="bg-slate-50 rounded-lg p-3">
                    <p class="text-xs text-slate-400 mb-1">Criado por</p>
                    <p class="text-sm font-medium text-slate-700"><?= Html::encode($task->creator->name ?? '') ?></p>
                </div>
                <div class="bg-slate-50 rounded-lg p-3">
                    <p class="text-xs text-slate-400 mb-1">Criado em</p>
                    <p class="text-sm font-medium text-slate-700"><?= date('d/m/Y H:i', $task->created_at) ?></p>
                </div>
            </div>

            <?php if ($task->isCompleted() && $task->completed_at): ?>
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3 flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                    <p class="text-sm text-emerald-700">Concluída em <?= date('d/m/Y H:i', $task->completed_at) ?></p>
                </div>
            <?php endif; ?>

            <!-- Anexos -->
            <?php $attachments = $task->attachments; ?>
            <?php if (!empty($attachments)): ?>
                <div>
                    <h4 class="text-sm font-medium text-slate-500 mb-3">Anexos (<?= count($attachments) ?>)</h4>
                    <div class="space-y-2">
                        <?php foreach ($attachments as $att): ?>
                            <div class="flex items-center justify-between bg-slate-50 rounded-lg px-4 py-3">
                                <a href="<?= Html::encode($att->getDownloadUrl()) ?>" target="_blank"
                                   class="flex items-center gap-3 text-sm text-slate-700 hover:text-primary-600 transition-colors">
                                    <i data-lucide="<?= $att->isImage() ? 'image' : 'file-text' ?>" class="w-5 h-5 text-slate-400"></i>
                                    <div>
                                        <p class="font-medium"><?= Html::encode($att->original_name) ?></p>
                                        <p class="text-xs text-slate-400"><?= $att->getFormattedSize() ?></p>
                                    </div>
                                </a>
                                <form method="post" action="<?= Url::to(['/task/delete-attachment', 'id' => $att->id]) ?>" class="inline">
                                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                    <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Remover?')">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
