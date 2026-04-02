<?php
/**
 * View: Formulário de Login
 *
 * @var yii\web\View $this
 * @var app\models\LoginForm $model
 */

use yii\helpers\Html;
use yii\helpers\Url;
?>

<h2 class="text-2xl font-bold text-slate-800 mb-1">Entrar</h2>
<p class="text-slate-500 text-sm mb-6">Acesse sua conta para gerenciar projetos e tarefas</p>

<?php if ($model->hasErrors()): ?>
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
        <?= Html::encode($model->getFirstError('password') ?: $model->getFirstError('email')) ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= Url::to(['/auth/login']) ?>" class="space-y-5">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

    <!-- Email -->
    <div>
        <label for="loginform-email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
        <input type="email" id="loginform-email" name="LoginForm[email]"
               value="<?= Html::encode($model->email) ?>"
               class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm"
               placeholder="seu@email.com" required>
    </div>

    <!-- Senha -->
    <div>
        <label for="loginform-password" class="block text-sm font-medium text-slate-700 mb-1.5">Senha</label>
        <input type="password" id="loginform-password" name="LoginForm[password]"
               class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm"
               placeholder="••••••••" required>
    </div>

    <!-- Lembrar-me -->
    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="LoginForm[rememberMe]" value="1"
                   <?= $model->rememberMe ? 'checked' : '' ?>
                   class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
            <span class="text-sm text-slate-600">Manter-me conectado</span>
        </label>
    </div>

    <!-- Botão -->
    <button type="submit"
            class="w-full bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-200 shadow-lg shadow-primary-600/25 hover:shadow-primary-700/30">
        Entrar
    </button>
</form>

<!-- Link para registro -->
<div class="mt-6 text-center">
    <span class="text-sm text-slate-500">Não tem conta?</span>
    <a href="<?= Url::to(['/auth/register']) ?>" class="text-sm font-medium text-primary-600 hover:text-primary-700 ml-1">
        Criar conta gratuita
    </a>
</div>
