<?php
/**
 * View: Formulário de Projeto (usado em create e update)
 *
 * @var yii\web\View $this
 * @var app\models\Project $model
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
        <span class="text-slate-800"><?= $isUpdate ? 'Editar' : 'Novo Projeto' ?></span>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-xl font-bold text-slate-800"><?= $isUpdate ? 'Editar Projeto' : 'Novo Projeto' ?></h2>
            <p class="text-sm text-slate-500 mt-1">Preencha as informações do projeto</p>
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
                <label for="project-title" class="block text-sm font-medium text-slate-700 mb-1.5">Título *</label>
                <input type="text" id="project-title" name="Project[title]"
                       value="<?= Html::encode($model->title) ?>"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm"
                       placeholder="Nome do projeto" required>
            </div>

            <!-- Descrição -->
            <div>
                <label for="project-description" class="block text-sm font-medium text-slate-700 mb-1.5">Descrição</label>
                <textarea id="project-description" name="Project[description]" rows="4"
                          class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm resize-none"
                          placeholder="Descreva o objetivo do projeto..."><?= Html::encode($model->description) ?></textarea>
            </div>

            <!-- Datas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="project-start_date" class="block text-sm font-medium text-slate-700 mb-1.5">Data de Início</label>
                    <input type="date" id="project-start_date" name="Project[start_date]"
                           value="<?= Html::encode($model->start_date) ?>"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm">
                </div>
                <div>
                    <label for="project-end_date" class="block text-sm font-medium text-slate-700 mb-1.5">Data de Conclusão</label>
                    <input type="date" id="project-end_date" name="Project[end_date]"
                           value="<?= Html::encode($model->end_date) ?>"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm">
                </div>
            </div>

            <!-- Upload de Arquivos -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Anexos (PDF ou Imagem)</label>
                <div class="border-2 border-dashed border-slate-300 rounded-lg p-6 text-center hover:border-primary-400 transition-colors">
                    <i data-lucide="upload-cloud" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
                    <p class="text-sm text-slate-500 mb-2">Arraste arquivos ou clique para selecionar</p>
                    <input type="file" name="Project[attachmentFiles][]" multiple
                           accept=".pdf,.png,.jpg,.jpeg,.gif"
                           class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                    <p class="text-xs text-slate-400 mt-2">PDF, PNG, JPG, GIF — Máximo 10MB por arquivo</p>
                </div>
            </div>

            <!-- Anexos existentes -->
            <?php if ($isUpdate && !empty($model->attachments)): ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Anexos Atuais</label>
                    <div class="space-y-2">
                        <?php foreach ($model->attachments as $att): ?>
                            <div class="flex items-center justify-between bg-slate-50 rounded-lg px-4 py-2.5">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="<?= $att->isImage() ? 'image' : 'file-text' ?>" class="w-4 h-4 text-slate-500"></i>
                                    <span class="text-sm text-slate-700"><?= Html::encode($att->original_name) ?></span>
                                    <span class="text-xs text-slate-400"><?= $att->getFormattedSize() ?></span>
                                </div>
                                <form method="post" action="<?= Url::to(['/project/delete-attachment', 'id' => $att->id]) ?>" class="inline">
                                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors"
                                            onclick="return confirm('Remover este anexo?')">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Ações -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="<?= Url::to(['/project/index']) ?>"
                   class="px-4 py-2.5 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-all shadow-sm hover:shadow-md">
                    <?= $isUpdate ? 'Salvar Alterações' : 'Criar Projeto' ?>
                </button>
            </div>
        </form>
    </div>
</div>
