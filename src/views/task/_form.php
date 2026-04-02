<?php
/**
 * View: Formulário de Tarefa (usado em create e update)
 *
 * @var yii\web\View $this
 * @var app\models\Task $model
 * @var app\models\Project $project
 * @var array $members
 */

use yii\helpers\Html;
use yii\helpers\Url;

$isUpdate = !$model->isNewRecord;
?>

<div class="max-w-2xl mx-auto">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="<?= Url::to(['/project/index']) ?>" class="hover:text-primary-600 transition-colors">Projetos</a>
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
        <a href="<?= Url::to(['/project/view', 'id' => $project->id]) ?>" class="hover:text-primary-600 transition-colors"><?= Html::encode($project->title) ?></a>
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
        <span class="text-slate-800"><?= $isUpdate ? 'Editar Tarefa' : 'Nova Tarefa' ?></span>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-xl font-bold text-slate-800"><?= $isUpdate ? 'Editar Tarefa' : 'Nova Tarefa' ?></h2>
        </div>

        <?php if ($model->hasErrors()): ?>
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc pl-4 space-y-1">
                    <?php foreach ($model->getFirstErrors() as $error): ?>
                        <li><?= Html::encode($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="p-6 space-y-5">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

            <!-- Título -->
            <div>
                <label for="task-title" class="block text-sm font-medium text-slate-700 mb-1.5">Título *</label>
                <input type="text" id="task-title" name="Task[title]"
                       value="<?= Html::encode($model->title) ?>"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm"
                       placeholder="O que precisa ser feito?" required>
            </div>

            <!-- Descrição -->
            <div>
                <label for="task-description" class="block text-sm font-medium text-slate-700 mb-1.5">Descrição</label>
                <textarea id="task-description" name="Task[description]" rows="4"
                          class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm resize-none"
                          placeholder="Detalhe a tarefa..."><?= Html::encode($model->description) ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Data de Vencimento -->
                <div>
                    <label for="task-due_date" class="block text-sm font-medium text-slate-700 mb-1.5">Data de Vencimento</label>
                    <input type="date" id="task-due_date" name="Task[due_date]"
                           value="<?= Html::encode($model->due_date) ?>"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm">
                </div>

                <!-- Responsável -->
                <div>
                    <label for="task-assigned_to" class="block text-sm font-medium text-slate-700 mb-1.5">Responsável</label>
                    <select id="task-assigned_to" name="Task[assigned_to]"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm">
                        <option value="">— Sem responsável —</option>
                        <?php foreach ($members as $id => $name): ?>
                            <option value="<?= $id ?>" <?= $model->assigned_to == $id ? 'selected' : '' ?>>
                                <?= Html::encode($name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Prioridade -->
                <div>
                    <label for="task-priority" class="block text-sm font-medium text-slate-700 mb-1.5">Prioridade</label>
                    <select id="task-priority" name="Task[priority]"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm">
                        <?php foreach (Yii::$app->params['taskPriorities'] as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $model->priority === $key ? 'selected' : '' ?>>
                                <?= Html::encode($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label for="task-status" class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                    <select id="task-status" name="Task[status]"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm">
                        <?php foreach (Yii::$app->params['taskStatuses'] as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $model->status === $key ? 'selected' : '' ?>>
                                <?= Html::encode($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Upload de Arquivos -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Anexos (PDF ou Imagem)</label>
                <div class="border-2 border-dashed border-slate-300 rounded-lg p-6 text-center hover:border-primary-400 transition-colors">
                    <i data-lucide="upload-cloud" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
                    <input type="file" name="Task[attachmentFiles][]" multiple
                           accept=".pdf,.png,.jpg,.jpeg,.gif"
                           class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                    <p class="text-xs text-slate-400 mt-2">PDF, PNG, JPG, GIF — Máximo 10MB</p>
                </div>
            </div>

            <!-- Anexos existentes -->
            <?php if ($isUpdate): ?>
                <?php $attachments = $model->attachments; ?>
                <?php if (!empty($attachments)): ?>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Anexos Atuais</label>
                        <div class="space-y-2">
                            <?php foreach ($attachments as $att): ?>
                                <div class="flex items-center justify-between bg-slate-50 rounded-lg px-4 py-2.5">
                                    <div class="flex items-center gap-3">
                                        <i data-lucide="<?= $att->isImage() ? 'image' : 'file-text' ?>" class="w-4 h-4 text-slate-500"></i>
                                        <a href="<?= Html::encode($att->getDownloadUrl()) ?>" target="_blank"
                                           class="text-sm text-slate-700 hover:text-primary-600"><?= Html::encode($att->original_name) ?></a>
                                        <span class="text-xs text-slate-400"><?= $att->getFormattedSize() ?></span>
                                    </div>
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
            <?php endif; ?>

            <!-- Ações -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="<?= Url::to(['/project/view', 'id' => $project->id]) ?>"
                   class="px-4 py-2.5 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-all shadow-sm hover:shadow-md">
                    <?= $isUpdate ? 'Salvar Alterações' : 'Criar Tarefa' ?>
                </button>
            </div>
        </form>
    </div>
</div>
