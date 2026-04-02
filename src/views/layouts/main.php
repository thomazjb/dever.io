<?php

/**
 * Layout principal da aplicação Dever.io
 *
 * Layout responsivo com Tailwind CSS incluindo:
 * - Navbar superior com menu do usuário
 * - Sidebar com navegação principal
 * - Área de conteúdo principal
 *
 * @var yii\web\View $this
 * @var string $content Conteúdo da página
 */

use yii\helpers\Html;
use yii\helpers\Url;

/** @var \app\models\User|null $user */
$user = Yii::$app->user->identity;
$isGuest = Yii::$app->user->isGuest;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-full">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dever.io - Sistema de Gerenciamento de Tarefas para Desenvolvedores">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title ? $this->title . ' | Dever.io' : 'Dever.io') ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        },
                        dark: {
                            700: '#1e293b',
                            800: '#1a1f2e',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Animação suave de transição para sidebar */
        .sidebar-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Efeito hover nos links da sidebar */
        .nav-link {
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            transform: translateX(4px);
        }

        /* Efeito glassmorphism para cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Scrollbar customizada */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        /* Badge animado de notificação */
        @keyframes pulse-dot {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }
        }

        .pulse-badge {
            animation: pulse-dot 2s ease-in-out infinite;
        }
    </style>

    <?php $this->head() ?>
</head>

<body class="h-full bg-slate-50">
    <?php $this->beginBody() ?>

    <?php if ($isGuest): ?>
        <!-- ===== LAYOUT PARA VISITANTES (Login/Registro) ===== -->
        <div class="min-h-screen bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900 flex items-center justify-center p-4">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center gap-2 mb-3">
                        <div class="w-9 h-9 flex items-center justify-center">
                
                                <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8">

                                    <defs>
                                        <linearGradient id="gradTop" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="#34d399" />
                                            <stop offset="100%" stop-color="#22c55e" />
                                        </linearGradient>
                                        <linearGradient id="gradBottom" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="#3b82f6" />
                                            <stop offset="100%" stop-color="#6366f1" />
                                        </linearGradient>
                                    </defs>

                                    <!-- Topo -->
                                    <path d="M16 16 
           C16 10, 22 10, 22 10 
           H42 
           C42 10, 48 10, 48 16"
                                        stroke="url(#gradTop)"
                                        stroke-width="5"
                                        fill="none"
                                        stroke-linecap="round" />

                                    <!-- Base -->
                                    <path d="M16 48 
           C16 54, 22 54, 22 54 
           H42 
           C42 54, 48 54, 48 48"
                                        stroke="url(#gradBottom)"
                                        stroke-width="5"
                                        fill="none"
                                        stroke-linecap="round" />

                                    <!-- Lado esquerdo -->
                                    <path d="M16 16 
           C10 16, 10 22, 10 22 
           V42 
           C10 42, 10 48, 16 48"
                                        stroke="url(#gradTop)"
                                        stroke-width="5"
                                        fill="none"
                                        stroke-linecap="round" />

                                    <!-- Lado direito -->
                                    <path d="M48 16 
           C54 16, 54 22, 54 22 
           V42 
           C54 42, 54 48, 48 48"
                                        stroke="url(#gradBottom)"
                                        stroke-width="5"
                                        fill="none"
                                        stroke-linecap="round" />

                                    <!-- Olho X (maior) -->
                                    <line x1="24" y1="28" x2="30" y2="34" stroke="#fb923c" stroke-width="3.5" stroke-linecap="round" />
                                    <line x1="30" y1="28" x2="24" y2="34" stroke="#fb923c" stroke-width="3.5" stroke-linecap="round" />

                                    <!-- Olho (maior) -->
                                    <circle cx="40" cy="31" r="3.2" fill="#fb923c" />

                                    <!-- Sorriso (mais largo) -->
                                    <path d="M24 42 Q32 50 40 42"
                                        stroke="#fb923c"
                                        stroke-width="3.5"
                                        fill="none"
                                        stroke-linecap="round" />

                                </svg>
                            </div>

                        </div>
                        <span class="text-3xl font-bold text-white">Dever.io</span>
                    </div>
                    <p class="text-primary-200 text-sm">Gerencie tarefas, colabore e acompanhe o progresso</p>
                </div>

                <!-- Card de conteúdo -->
                <div class="bg-white rounded-2xl shadow-2xl p-8">
                    <?= $content ?>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- ===== LAYOUT PARA USUÁRIOS AUTENTICADOS ===== -->
        <div class="flex h-screen overflow-hidden">

            <!-- ===== SIDEBAR ===== -->
            <aside id="sidebar" class="sidebar-transition w-64 bg-dark-900 text-white flex flex-col shadow-xl">
                <!-- Logo -->
                <div class="p-5 border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 flex items-center justify-center">
                            <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8">

                                <defs>
                                    <linearGradient id="gradTop" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="#34d399" />
                                        <stop offset="100%" stop-color="#22c55e" />
                                    </linearGradient>
                                    <linearGradient id="gradBottom" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="#3b82f6" />
                                        <stop offset="100%" stop-color="#6366f1" />
                                    </linearGradient>
                                </defs>

                                <!-- Topo -->
                                <path d="M16 16 
                               C16 10, 22 10, 22 10 
                               H42 
                               C42 10, 48 10, 48 16"
                                    stroke="url(#gradTop)"
                                    stroke-width="5"
                                    fill="none"
                                    stroke-linecap="round" />

                                <!-- Base -->
                                <path d="M16 48 
                               C16 54, 22 54, 22 54 
                               H42 
                               C42 54, 48 54, 48 48"
                                    stroke="url(#gradBottom)"
                                    stroke-width="5"
                                    fill="none"
                                    stroke-linecap="round" />

                                <!-- Lado esquerdo -->
                                <path d="M16 16 
                               C10 16, 10 22, 10 22 
                               V42 
                               C10 42, 10 48, 16 48"
                                    stroke="url(#gradTop)"
                                    stroke-width="5"
                                    fill="none"
                                    stroke-linecap="round" />

                                <!-- Lado direito -->
                                <path d="M48 16 
                               C54 16, 54 22, 54 22 
                               V42 
                               C54 42, 54 48, 48 48"
                                    stroke="url(#gradBottom)"
                                    stroke-width="5"
                                    fill="none"
                                    stroke-linecap="round" />

                                <!-- Olho X -->
                                <line x1="26" y1="30" x2="30" y2="34" stroke="#fb923c" stroke-width="3" stroke-linecap="round" />
                                <line x1="30" y1="30" x2="26" y2="34" stroke="#fb923c" stroke-width="3" stroke-linecap="round" />

                                <!-- Olho -->
                                <circle cx="38" cy="32" r="2.5" fill="#fb923c" />

                                <!-- Sorriso -->
                                <path d="M26 40 Q32 46 38 40"
                                    stroke="#fb923c"
                                    stroke-width="3"
                                    fill="none"
                                    stroke-linecap="round" />

                            </svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight">Dever.io</span>
                    </div>
                </div>

                <!-- Navegação principal -->
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                    <!-- Dashboard -->
                    <a href="<?= Url::to(['/dashboard/index']) ?>"
                        class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                          <?= Yii::$app->controller->id === 'dashboard' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                        Dashboard
                    </a>

                    <!-- Projetos -->
                    <a href="<?= Url::to(['/project/index']) ?>"
                        class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                          <?= Yii::$app->controller->id === 'project' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
                        <i data-lucide="folder-kanban" class="w-5 h-5"></i>
                        Projetos
                    </a>

                    <!-- Minhas Tarefas -->
                    <a href="<?= Url::to(['/task/my-tasks']) ?>"
                        class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                          <?= Yii::$app->controller->id === 'task' && Yii::$app->controller->action->id === 'my-tasks' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
                        <i data-lucide="check-square" class="w-5 h-5"></i>
                        Minhas Tarefas
                    </a>

                    <!-- Separador -->
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Atividade</p>
                    </div>

                    <!-- Tarefas Pendentes -->
                    <a href="<?= Url::to(['/dashboard/index', 'filter' => 'pending']) ?>"
                        class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-400 hover:text-white hover:bg-white/5">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                        Pendentes
                        <span class="ml-auto bg-amber-500/20 text-amber-400 text-xs font-bold px-2 py-0.5 rounded-full" id="pending-count"></span>
                    </a>

                    <!-- Tarefas Atrasadas -->
                    <a href="<?= Url::to(['/dashboard/index', 'filter' => 'overdue']) ?>"
                        class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-400 hover:text-white hover:bg-white/5">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                        Atrasadas
                        <span class="ml-auto bg-red-500/20 text-red-400 text-xs font-bold px-2 py-0.5 rounded-full pulse-badge" id="overdue-count"></span>
                    </a>
                </nav>

                <!-- Perfil do usuário na sidebar -->
                <div class="p-4 border-t border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center text-sm font-bold text-white">
                            <?= strtoupper(substr($user->name ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate"><?= Html::encode($user->name ?? 'Usuário') ?></p>
                            <p class="text-xs text-slate-500 truncate"><?= Html::encode($user->email ?? '') ?></p>
                        </div>
                        <form method="post" action="<?= Url::to(['/auth/logout']) ?>" class="inline">
                            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                            <button type="submit" class="text-slate-500 hover:text-red-400 transition-colors" title="Sair">
                                <i data-lucide="log-out" class="w-5 h-5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- ===== CONTEÚDO PRINCIPAL ===== -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Top bar -->
                <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 shadow-sm">
                    <!-- Botão mobile sidebar -->
                    <button id="sidebar-toggle" class="lg:hidden text-slate-500 hover:text-slate-700">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>

                    <!-- Breadcrumb / Título da página -->
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg font-semibold text-slate-800"><?= Html::encode($this->title ?? '') ?></h1>
                    </div>

                    <!-- Ações do header -->
                    <div class="flex items-center gap-3">
                        <a href="<?= Url::to(['/project/create']) ?>"
                            class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-all hover:shadow-lg hover:shadow-primary-600/25">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Novo Projeto
                        </a>
                    </div>
                </header>

                <!-- Área de conteúdo com scroll -->
                <main class="flex-1 overflow-y-auto p-6 bg-slate-50">
                    <!-- Flash messages -->
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl" role="alert">
                            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
                            <span class="text-sm font-medium"><?= Html::encode(Yii::$app->session->getFlash('success')) ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl" role="alert">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
                            <span class="text-sm font-medium"><?= Html::encode(Yii::$app->session->getFlash('error')) ?></span>
                        </div>
                    <?php endif; ?>

                    <?= $content ?>
                </main>
            </div>
        </div>

        <!-- Script para toggle sidebar mobile -->
        <script>
            document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.toggle('-translate-x-full');
            });
        </script>
    <?php endif; ?>

    <!-- Inicializar ícones Lucide -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>