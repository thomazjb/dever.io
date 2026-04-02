<?php
/**
 * View: Formulário de Registro
 *
 * @var yii\web\View $this
 * @var app\models\RegisterForm $model
 */

use yii\helpers\Html;
use yii\helpers\Url;
?>

<h2 class="text-2xl font-bold text-slate-800 mb-1">Criar Conta</h2>
<p class="text-slate-500 text-sm mb-6">Junte-se ao Dever.io e organize seus projetos</p>

<?php if ($model->hasErrors()): ?>
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
        <ul class="list-disc pl-4 space-y-1">
            <?php foreach ($model->getFirstErrors() as $error): ?>
                <li><?= Html::encode($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= Url::to(['/auth/register']) ?>" class="space-y-5">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

    <!-- Nome -->
    <div>
        <label for="registerform-name" class="block text-sm font-medium text-slate-700 mb-1.5">Nome Completo</label>
        <input type="text" id="registerform-name" name="RegisterForm[name]"
               value="<?= Html::encode($model->name) ?>"
               class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm"
               placeholder="Seu nome completo" required>
    </div>

    <!-- Email -->
    <div>
        <label for="registerform-email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
        <input type="email" id="registerform-email" name="RegisterForm[email]"
               value="<?= Html::encode($model->email) ?>"
               class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm"
               placeholder="seu@email.com" required>
    </div>

    <!-- Senha -->
    <div>
        <label for="registerform-password" class="block text-sm font-medium text-slate-700 mb-1.5">Senha</label>
        <input type="password" id="registerform-password" name="RegisterForm[password]"
               class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm"
               placeholder="Mínimo 6 caracteres" required minlength="6">
    </div>

    <!-- Confirmar Senha -->
    <div>
        <label for="registerform-password_confirm" class="block text-sm font-medium text-slate-700 mb-1.5">Confirmar Senha</label>
        <input type="password" id="registerform-password_confirm" name="RegisterForm[password_confirm]"
               class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm"
               placeholder="Repita a senha" required>
    </div>

    <!-- Botão -->
    <button type="submit"
            class="w-full bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-200 shadow-lg shadow-primary-600/25 hover:shadow-primary-700/30">
        Criar Conta
    </button>
</form>

<!-- Link para login -->
<div class="mt-6 text-center">
    <span class="text-sm text-slate-500">Já tem conta?</span>
    <a href="<?= Url::to(['/auth/login']) ?>" class="text-sm font-medium text-primary-600 hover:text-primary-700 ml-1">
        Entrar
    </a>
</div>
