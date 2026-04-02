<?php
/**
 * View: Listagem de Projetos
 *
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
?>

<!-- Cabeçalho -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Projetos</h2>
        <p class="text-sm text-slate-500 mt-1"><?= $dataProvider->getTotalCount() ?> projetos encontrados</p>
    </div>
    <a href="<?= Url::to(['/project/create']) ?>"
       class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-all shadow-sm hover:shadow-md">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Novo Projeto
    </a>
</div>

<!-- Grid de Projetos -->
<?php if ($dataProvider->getCount() === 0): ?>
    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
        <i data-lucide="folder-open" class="w-16 h-16 text-slate-300 mx-auto mb-4"></i>
        <h3 class="text-lg font-semibold text-slate-600 mb-2">Nenhum projeto ainda</h3>
        <p class="text-slate-400 mb-6">Crie seu primeiro projeto para começar a organizar suas tarefas</p>
        <a href="<?= Url::to(['/project/create']) ?>"
           class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-5 py-2.5 rounded-lg transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Criar Projeto
        </a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($dataProvider->getModels() as $project): ?>
            <div class="bg-white rounded-xl border border-slate-200 hover:shadow-lg hover:border-primary-200 transition-all duration-300 flex flex-col">
                <!-- Header do Card -->
                <div class="p-5 flex-1">
                    <div class="flex items-start justify-between mb-3">
                        <a href="<?= Url::to(['/project/view', 'id' => $project->id]) ?>"
                           class="font-semibold text-slate-800 hover:text-primary-600 transition-colors text-lg leading-tight">
                            <?= Html::encode($project->title) ?>
                        </a>
                        <?php if ($project->isOwner()): ?>
                            <span class="ml-2 text-xs bg-primary-100 text-primary-700 px-2 py-0.5 rounded-full whitespace-nowrap">Dono</span>
                        <?php else: ?>
                            <span class="ml-2 text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full whitespace-nowrap">Membro</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($project->description): ?>
                        <p class="text-sm text-slate-500 line-clamp-3 mb-4"><?= Html::encode(mb_substr($project->description, 0, 150)) ?></p>
                    <?php endif; ?>

                    <!-- Datas -->
                    <div class="flex items-center gap-4 text-xs text-slate-400 mb-4">
                        <?php if ($project->start_date): ?>
                            <span class="flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                <?= date('d/m/Y', strtotime($project->start_date)) ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($project->end_date): ?>
                            <span class="flex items-center gap-1">
                                <i data-lucide="calendar-check" class="w-3.5 h-3.5"></i>
                                <?= date('d/m/Y', strtotime($project->end_date)) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Footer do Card -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 rounded-b-xl">
                    <!-- Barra de progresso -->
                    <?php $progress = $project->getProgress(); ?>
                    <div class="flex items-center justify-between text-xs text-slate-400 mb-1.5">
                        <span><?= $project->getTaskCount() ?> tarefas</span>
                        <span class="font-medium"><?= $progress ?>%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-1.5">
                        <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-1.5 rounded-full transition-all duration-500"
                             style="width: <?= $progress ?>%"></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginação -->
    <div class="mt-6 flex justify-center">
        <?= LinkPager::widget([
            'pagination' => $dataProvider->pagination,
            'options' => ['class' => 'flex items-center gap-1'],
            'linkOptions' => ['class' => 'px-3 py-1.5 text-sm rounded-lg border border-slate-200 hover:bg-primary-50 hover:text-primary-600 transition-colors'],
            'activePageCssClass' => 'bg-primary-600 text-white border-primary-600',
            'disabledPageCssClass' => 'opacity-50 cursor-not-allowed',
        ]) ?>
    </div>
<?php endif; ?>
