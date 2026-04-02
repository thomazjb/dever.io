<?php
/**
 * View: Criar nova tarefa
 * @var yii\web\View $this
 * @var app\models\Task $model
 * @var app\models\Project $project
 * @var array $members
 */
echo $this->render('_form', ['model' => $model, 'project' => $project, 'members' => $members]);
