<?php
/**
 * View: Perfil do usuário
 *
 * @var yii\web\View $this
 * @var app\models\User $model
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Meu Perfil';
?>
<div class="max-w-2xl mx-auto">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="<?= Url::to(['/site/index']) ?>" class="hover:text-primary-600 transition-colors">Início</a>
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
        <span class="text-slate-800">Perfil</span>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-xl font-bold text-slate-800">Editar Perfil</h2>
            <p class="text-sm text-slate-500 mt-1">Atualize suas informações pessoais</p>
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

        <?php $form = yii\widgets\ActiveForm::begin([
            'id' => 'profile-form',
            'options' => ['class' => 'p-6 space-y-5']
        ]); ?>

            <!-- Nome -->
            <div>
                <label for="user-name" class="block text-sm font-medium text-slate-700 mb-1.5">Nome completo *</label>
                <?= $form->field($model, 'name')->textInput([
                    'id' => 'user-name',
                    'class' => 'w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm',
                    'placeholder' => 'Nome completo'
                ])->label(false) ?>
            </div>

            <!-- Email -->
            <div>
                <label for="user-email" class="block text-sm font-medium text-slate-700 mb-1.5">Email *</label>
                <?= $form->field($model, 'email')->input('email', [
                    'id' => 'user-email',
                    'class' => 'w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm',
                    'placeholder' => 'seu@email.com'
                ])->label(false) ?>
            </div>

            <!-- Senha -->
            <div>
                <label for="user-password" class="block text-sm font-medium text-slate-700 mb-1.5">Nova senha (deixe em branco para manter a atual)</label>
                <?= $form->field($model, 'password')->passwordInput([
                    'id' => 'user-password',
                    'class' => 'w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm',
                    'placeholder' => 'Mínimo 8 caracteres'
                ])->label(false) ?>
            </div>

            <!-- Confirmação de senha -->
            <div>
                <label for="user-password_repeat" class="block text-sm font-medium text-slate-700 mb-1.5">Confirmar nova senha</label>
                <?= $form->field($model, 'password_repeat')->passwordInput([
                    'id' => 'user-password_repeat',
                    'class' => 'w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm',
                    'placeholder' => 'Repita a senha'
                ])->label(false) ?>
            </div>

            <!-- Ações -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="<?= Url::to(['/site/index']) ?>"
                   class="px-4 py-2.5 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-all shadow-sm hover:shadow-md">
                    Salvar Alterações
                </button>
            </div>

        <?php yii\widgets\ActiveForm::end(); ?>
    </div>
</div>