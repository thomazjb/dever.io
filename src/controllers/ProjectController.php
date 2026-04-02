<?php
/**
 * ProjectController - CRUD completo de projetos
 *
 * Gerencia projetos com listagem, criação, edição, exclusão,
 * gerenciamento de membros e controle de autorização.
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
use app\models\Project;
use app\models\ProjectUser;
use app\models\Task;
use app\models\Attachment;
use app\models\User;

class ProjectController extends Controller
{
    /**
     * Behaviors: autenticação obrigatória e verbos HTTP.
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Apenas autenticados
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'add-member' => ['POST'],
                    'remove-member' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Listagem de projetos do usuário (dono ou membro).
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;

        $dataProvider = new ActiveDataProvider([
            'query' => Project::find()
                ->joinWith('projectUsers')
                ->where(['project_user.user_id' => $userId])
                ->orderBy(['project.created_at' => SORT_DESC]),
            'pagination' => ['pageSize' => 12],
        ]);

        $this->view->title = 'Projetos';
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Visualizar detalhes de um projeto.
     *
     * @param int $id
     */
    public function actionView($id)
    {
        $project = $this->findModel($id);
        $this->checkAccess($project);

        // Tarefas do projeto
        $taskDataProvider = new ActiveDataProvider([
            'query' => Task::find()
                ->where(['project_id' => $project->id])
                ->orderBy(['due_date' => SORT_ASC, 'priority' => SORT_DESC]),
            'pagination' => ['pageSize' => 20],
        ]);

        $this->view->title = $project->title;
        return $this->render('view', [
            'project' => $project,
            'taskDataProvider' => $taskDataProvider,
        ]);
    }

    /**
     * Criar novo projeto.
     */
    public function actionCreate()
    {
        $model = new Project();
        $model->owner_id = Yii::$app->user->id;

        if ($model->load(Yii::$app->request->post())) {
            // Upload de anexos
            $uploadedFiles = UploadedFile::getInstances($model, 'attachmentFiles');

            if ($model->save()) {
                // Processar uploads
                foreach ($uploadedFiles as $file) {
                    $attachment = new Attachment();
                    $attachment->uploadFile($file, 'project', $model->id);
                }

                Yii::$app->session->setFlash('success', 'Projeto criado com sucesso!');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $this->view->title = 'Novo Projeto';
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Editar um projeto existente.
     *
     * @param int $id
     */
    public function actionUpdate($id)
    {
        $project = $this->findModel($id);
        $this->checkOwnership($project);

        if ($project->load(Yii::$app->request->post())) {
            $uploadedFiles = UploadedFile::getInstances($project, 'attachmentFiles');

            if ($project->save()) {
                foreach ($uploadedFiles as $file) {
                    $attachment = new Attachment();
                    $attachment->uploadFile($file, 'project', $project->id);
                }

                Yii::$app->session->setFlash('success', 'Projeto atualizado com sucesso!');
                return $this->redirect(['view', 'id' => $project->id]);
            }
        }

        $this->view->title = 'Editar: ' . $project->title;
        return $this->render('update', [
            'model' => $project,
        ]);
    }

    /**
     * Excluir um projeto.
     *
     * @param int $id
     */
    public function actionDelete($id)
    {
        $project = $this->findModel($id);
        $this->checkOwnership($project);

        $project->delete();
        Yii::$app->session->setFlash('success', 'Projeto excluído com sucesso!');
        return $this->redirect(['index']);
    }

    /**
     * Gerenciar membros do projeto.
     *
     * @param int $id
     */
    public function actionMembers($id)
    {
        $project = $this->findModel($id);
        $this->checkOwnership($project);

        $members = ProjectUser::find()
            ->with('user')
            ->where(['project_id' => $project->id])
            ->all();

        $this->view->title = 'Membros: ' . $project->title;
        return $this->render('members', [
            'project' => $project,
            'members' => $members,
        ]);
    }

    /**
     * Adicionar membro ao projeto (POST).
     *
     * @param int $id
     */
    public function actionAddMember($id)
    {
        $project = $this->findModel($id);
        $this->checkOwnership($project);

        $email = Yii::$app->request->post('email');
        if (empty($email)) {
            Yii::$app->session->setFlash('error', 'Informe o email do membro.');
            return $this->redirect(['members', 'id' => $id]);
        }

        $user = User::findByEmail($email);
        if (!$user) {
            Yii::$app->session->setFlash('error', 'Usuário não encontrado com este email.');
            return $this->redirect(['members', 'id' => $id]);
        }

        if ($project->addMember($user->id)) {
            Yii::$app->session->setFlash('success', "Membro {$user->name} adicionado com sucesso!");
        } else {
            Yii::$app->session->setFlash('error', 'Este usuário já é membro do projeto.');
        }

        return $this->redirect(['members', 'id' => $id]);
    }

    /**
     * Remover membro do projeto (POST).
     *
     * @param int $id
     */
    public function actionRemoveMember($id)
    {
        $project = $this->findModel($id);
        $this->checkOwnership($project);

        $userId = Yii::$app->request->post('user_id');
        if ($project->removeMember((int) $userId)) {
            Yii::$app->session->setFlash('success', 'Membro removido com sucesso!');
        } else {
            Yii::$app->session->setFlash('error', 'Não é possível remover o dono do projeto.');
        }

        return $this->redirect(['members', 'id' => $id]);
    }

    /**
     * Deletar anexo de projeto.
     *
     * @param int $id Attachment ID
     */
    public function actionDeleteAttachment($id)
    {
        $attachment = Attachment::findOne($id);
        if (!$attachment || $attachment->entity_type !== 'project') {
            throw new NotFoundHttpException('Anexo não encontrado.');
        }

        $project = $this->findModel($attachment->entity_id);
        $this->checkAccess($project);

        $projectId = $attachment->entity_id;
        $attachment->delete();

        Yii::$app->session->setFlash('success', 'Anexo removido com sucesso!');
        return $this->redirect(['view', 'id' => $projectId]);
    }

    // =============================================
    // Métodos auxiliares
    // =============================================

    /**
     * Encontra o model pelo ID ou lança 404.
     */
    protected function findModel($id): Project
    {
        $model = Project::findOne((int) $id);
        if ($model === null) {
            throw new NotFoundHttpException('Projeto não encontrado.');
        }
        return $model;
    }

    /**
     * Verifica se o usuário tem acesso ao projeto.
     */
    protected function checkAccess(Project $project): void
    {
        if (!$project->hasAccess()) {
            throw new ForbiddenHttpException('Você não tem acesso a este projeto.');
        }
    }

    /**
     * Verifica se o usuário é dono do projeto.
     */
    protected function checkOwnership(Project $project): void
    {
        if (!$project->isOwner()) {
            throw new ForbiddenHttpException('Apenas o dono do projeto pode realizar esta ação.');
        }
    }
}
