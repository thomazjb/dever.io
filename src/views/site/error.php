<?php
/**
 * View: Página de erro
 *
 * @var yii\web\View $this
 * @var string $name
 * @var string $message
 * @var Exception $exception
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $name;
$statusCode = isset($exception) ? $exception->statusCode ?? 500 : 500;
?>

<div class="flex items-center justify-center min-h-[60vh]">
    <div class="text-center">
        <p class="text-8xl font-bold text-primary-600 mb-4"><?= $statusCode ?></p>
        <h1 class="text-2xl font-bold text-slate-800 mb-2"><?= Html::encode($name) ?></h1>
        <p class="text-slate-500 mb-8 max-w-md mx-auto"><?= Html::encode($message) ?></p>
        <a href="<?= Url::to(['/dashboard/index']) ?>"
           class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-5 py-2.5 rounded-lg transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Voltar ao Dashboard
        </a>
    </div>
</div>
