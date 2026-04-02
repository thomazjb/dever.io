<?php
/**
 * DashboardController - Painel de controle do usuário
 *
 * Exibe resumo de tarefas pendentes, atrasadas e
 * estatísticas dos projetos do usuário autenticado.
 *
 * @package app\controllers
 */

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use app\models\Task;
use app\models\Project;
use app\models\ProjectUser;

class DashboardController extends Controller
{
    /**
     * Apenas usuários autenticados.
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
        ];
    }

    /**
     * Dashboard principal com resumo de atividades.
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;

        // IDs dos projetos do usuário
        $projectIds = ProjectUser::find()
            ->select('project_id')
            ->where(['user_id' => $userId])
            ->column();

        // Contadores otimizados
        $totalTasks = Task::find()
            ->where(['project_id' => $projectIds])
            ->andWhere(['assigned_to' => $userId])
            ->count();

        $pendingCount = Task::find()
            ->where(['project_id' => $projectIds])
            ->andWhere(['assigned_to' => $userId])
            ->andWhere(['status' => 'pending'])
            ->count();

        $inProgressCount = Task::find()
            ->where(['project_id' => $projectIds])
            ->andWhere(['assigned_to' => $userId])
            ->andWhere(['status' => 'in_progress'])
            ->count();

        $completedCount = Task::find()
            ->where(['project_id' => $projectIds])
            ->andWhere(['assigned_to' => $userId])
            ->andWhere(['status' => 'completed'])
            ->count();

        $overdueCount = Task::find()
            ->where(['project_id' => $projectIds])
            ->andWhere(['assigned_to' => $userId])
            ->andWhere(['!=', 'status', 'completed'])
            ->andWhere(['<', 'due_date', date('Y-m-d')])
            ->andWhere(['is not', 'due_date', null])
            ->count();

        // Tarefas atrasadas (lista)
        $overdueTasks = new ActiveDataProvider([
            'query' => Task::find()
                ->with('project')
                ->where(['project_id' => $projectIds])
                ->andWhere(['assigned_to' => $userId])
                ->andWhere(['!=', 'status', 'completed'])
                ->andWhere(['<', 'due_date', date('Y-m-d')])
                ->andWhere(['is not', 'due_date', null])
                ->orderBy(['due_date' => SORT_ASC]),
            'pagination' => ['pageSize' => 10],
        ]);

        // Tarefas pendentes próximas do vencimento
        $upcomingTasks = new ActiveDataProvider([
            'query' => Task::find()
                ->with('project')
                ->where(['project_id' => $projectIds])
                ->andWhere(['assigned_to' => $userId])
                ->andWhere(['!=', 'status', 'completed'])
                ->andWhere(['>=', 'due_date', date('Y-m-d')])
                ->orderBy(['due_date' => SORT_ASC]),
            'pagination' => ['pageSize' => 10],
        ]);

        // Projetos do usuário
        $projects = Project::find()
            ->where(['id' => $projectIds])
            ->orderBy(['updated_at' => SORT_DESC])
            ->limit(6)
            ->all();

        $projectCount = count($projectIds);

        $this->view->title = 'Dashboard';
        return $this->render('index', [
            'totalTasks' => $totalTasks,
            'pendingCount' => $pendingCount,
            'inProgressCount' => $inProgressCount,
            'completedCount' => $completedCount,
            'overdueCount' => $overdueCount,
            'overdueTasks' => $overdueTasks,
            'upcomingTasks' => $upcomingTasks,
            'projects' => $projects,
            'projectCount' => $projectCount,
        ]);
    }
}
