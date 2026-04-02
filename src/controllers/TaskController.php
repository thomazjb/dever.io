<?php
/**
 * TaskController - CRUD completo de tarefas
 *
 * Gerencia tarefas dentro de projetos com filtros por status
 * e prioridade, marcação como concluída e upload de arquivos.
 *
 * @package app\controllers
 */

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\Task;
use app\models\Project;
use app\models\Attachment;
use app\models\User;

class TaskController extends Controller
{
    /**
     * Behaviors: autenticação obrigatória.
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'complete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Listagem de tarefas de um projeto com filtros.
     *
     * @param int $projectId
     */
    public function actionIndex($projectId)
    {
        $project = $this->findProject($projectId);

        $query = Task::find()->where(['project_id' => $project->id]);

        // Filtros via query params
        $status = Yii::$app->request->get('status');
        $priority = Yii::$app->request->get('priority');

        if ($status && in_array($status, ['pending', 'in_progress', 'completed'])) {
            $query->andWhere(['status' => $status]);
        }
        if ($priority && in_array($priority, ['low', 'medium', 'high'])) {
            $query->andWhere(['priority' => $priority]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['due_date' => SORT_ASC, 'created_at' => SORT_DESC]),
            'pagination' => ['pageSize' => 20],
        ]);

        $this->view->title = 'Tarefas: ' . $project->title;
        return $this->render('index', [
            'project' => $project,
            'dataProvider' => $dataProvider,
            'filterStatus' => $status,
            'filterPriority' => $priority,
        ]);
    }

    /**
     * Minhas tarefas - todas as tarefas atribuídas ao usuário.
     */
    public function actionMyTasks()
    {
        $userId = Yii::$app->user->id;

        $query = Task::find()
            ->with('project')
            ->where(['assigned_to' => $userId]);

        $status = Yii::$app->request->get('status');
        $priority = Yii::$app->request->get('priority');

        if ($status) {
            $query->andWhere(['status' => $status]);
        }
        if ($priority) {
            $query->andWhere(['priority' => $priority]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['due_date' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        $this->view->title = 'Minhas Tarefas';
        return $this->render('my-tasks', [
            'dataProvider' => $dataProvider,
            'filterStatus' => $status,
            'filterPriority' => $priority,
        ]);
    }

    /**
     * Visualizar detalhes de uma tarefa.
     *
     * @param int $projectId
     * @param int $id
     */
    public function actionView($projectId, $id)
    {
        $project = $this->findProject($projectId);
        $task = $this->findModel($id, $project->id);

        $this->view->title = $task->title;
        return $this->render('view', [
            'project' => $project,
            'task' => $task,
        ]);
    }

    /**
     * Criar nova tarefa.
     *
     * @param int $projectId
     */
    public function actionCreate($projectId)
    {
        $project = $this->findProject($projectId);

        $model = new Task();
        $model->project_id = $project->id;
        $model->created_by = Yii::$app->user->id;
        $model->assigned_to = Yii::$app->user->id;

        if ($model->load(Yii::$app->request->post())) {
            $uploadedFiles = UploadedFile::getInstances($model, 'attachmentFiles');

            if ($model->save()) {
                foreach ($uploadedFiles as $file) {
                    $attachment = new Attachment();
                    $attachment->uploadFile($file, 'task', $model->id);
                }

                Yii::$app->session->setFlash('success', 'Tarefa criada com sucesso!');
                return $this->redirect(['view', 'projectId' => $projectId, 'id' => $model->id]);
            }
        }

        // Membros do projeto para select de assignee
        $members = $project->getMembers()->select(['user.id', 'user.name'])->indexBy('id')->column();

        $this->view->title = 'Nova Tarefa';
        return $this->render('create', [
            'model' => $model,
            'project' => $project,
            'members' => $members,
        ]);
    }

    /**
     * Editar tarefa existente.
     *
     * @param int $projectId
     * @param int $id
     */
    public function actionUpdate($projectId, $id)
    {
        $project = $this->findProject($projectId);
        $task = $this->findModel($id, $project->id);

        if ($task->load(Yii::$app->request->post())) {
            $uploadedFiles = UploadedFile::getInstances($task, 'attachmentFiles');

            if ($task->save()) {
                foreach ($uploadedFiles as $file) {
                    $attachment = new Attachment();
                    $attachment->uploadFile($file, 'task', $task->id);
                }

                Yii::$app->session->setFlash('success', 'Tarefa atualizada com sucesso!');
                return $this->redirect(['view', 'projectId' => $projectId, 'id' => $task->id]);
            }
        }

        $members = $project->getMembers()->select(['user.id', 'user.name'])->indexBy('id')->column();

        $this->view->title = 'Editar: ' . $task->title;
        return $this->render('update', [
            'model' => $task,
            'project' => $project,
            'members' => $members,
        ]);
    }

    /**
     * Excluir tarefa.
     *
     * @param int $projectId
     * @param int $id
     */
    public function actionDelete($projectId, $id)
    {
        $project = $this->findProject($projectId);
        $task = $this->findModel($id, $project->id);

        $task->delete();
        Yii::$app->session->setFlash('success', 'Tarefa excluída com sucesso!');
        return $this->redirect(['index', 'projectId' => $projectId]);
    }

    /**
     * Marcar tarefa como concluída.
     *
     * @param int $projectId
     * @param int $id
     */
    public function actionComplete($projectId, $id)
    {
        $project = $this->findProject($projectId);
        $task = $this->findModel($id, $project->id);

        if ($task->markAsCompleted()) {
            Yii::$app->session->setFlash('success', 'Tarefa marcada como concluída!');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao concluir tarefa.');
        }

        $returnUrl = Yii::$app->request->referrer ?: ['index', 'projectId' => $projectId];
        return $this->redirect($returnUrl);
    }

    /**
     * Deletar anexo de tarefa.
     *
     * @param int $id Attachment ID
     */
    public function actionDeleteAttachment($id)
    {
        $attachment = Attachment::findOne($id);
        if (!$attachment || $attachment->entity_type !== 'task') {
            throw new NotFoundHttpException('Anexo não encontrado.');
        }

        $task = Task::findOne($attachment->entity_id);
        if (!$task) {
            throw new NotFoundHttpException('Tarefa não encontrada.');
        }

        $project = $this->findProject($task->project_id);

        $attachment->delete();
        Yii::$app->session->setFlash('success', 'Anexo removido com sucesso!');
        return $this->redirect(['view', 'projectId' => $task->project_id, 'id' => $task->id]);
    }

    // =============================================
    // Métodos auxiliares
    // =============================================

    /**
     * Encontra o projeto e verifica acesso.
     */
    protected function findProject($projectId): Project
    {
        $project = Project::findOne((int) $projectId);
        if (!$project) {
            throw new NotFoundHttpException('Projeto não encontrado.');
        }
        if (!$project->hasAccess()) {
            throw new ForbiddenHttpException('Você não tem acesso a este projeto.');
        }
        return $project;
    }

    /**
     * Encontra a tarefa pelo ID e project_id.
     */
    protected function findModel($id, $projectId): Task
    {
        $model = Task::findOne(['id' => (int) $id, 'project_id' => (int) $projectId]);
        if (!$model) {
            throw new NotFoundHttpException('Tarefa não encontrada.');
        }
        return $model;
    }
}
