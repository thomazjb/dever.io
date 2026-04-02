<?php
/**
 * View: Gerenciar Membros do Projeto
 *
 * @var yii\web\View $this
 * @var app\models\Project $project
 * @var app\models\ProjectUser[] $members
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
    <span class="text-slate-800">Membros</span>
</div>

<div class="max-w-2xl mx-auto">
    <!-- Adicionar Membro -->
    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-6">
        <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <i data-lucide="user-plus" class="w-5 h-5 text-primary-600"></i>
            Adicionar Membro
        </h3>
        <form method="post" action="<?= Url::to(['/project/add-member', 'id' => $project->id]) ?>" class="flex gap-3">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
            <input type="email" name="email"
                   class="flex-1 px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all text-sm"
                   placeholder="Digite o email do colaborador" required>
            <button type="submit"
                    class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-all whitespace-nowrap">
                Adicionar
            </button>
        </form>
    </div>

    <!-- Lista de Membros -->
    <div class="bg-white rounded-xl border border-slate-200">
        <div class="p-5 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                <i data-lucide="users" class="w-5 h-5"></i>
                Membros (<?= count($members) ?>)
            </h3>
        </div>
        <div class="divide-y divide-slate-100">
            <?php foreach ($members as $pu): ?>
                <div class="px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-sm font-bold text-white">
                            <?= strtoupper(substr($pu->user->name ?? 'U', 0, 1)) ?>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800"><?= Html::encode($pu->user->name ?? '') ?></p>
                            <p class="text-xs text-slate-400"><?= Html::encode($pu->user->email ?? '') ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full
                            <?= $pu->role === 'owner' ? 'bg-primary-100 text-primary-700' : 'bg-slate-100 text-slate-600' ?>">
                            <?= $pu->role === 'owner' ? 'Dono' : 'Membro' ?>
                        </span>
                        <?php if ($pu->role !== 'owner'): ?>
                            <form method="post" action="<?= Url::to(['/project/remove-member', 'id' => $project->id]) ?>" class="inline">
                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                <?= Html::hiddenInput('user_id', $pu->user_id) ?>
                                <button type="submit" class="text-red-500 hover:text-red-700 transition-colors"
                                        onclick="return confirm('Remover este membro?')" title="Remover membro">
                                    <i data-lucide="user-minus" class="w-4 h-4"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
