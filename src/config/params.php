<?php
/**
 * Parâmetros globais da aplicação Dever.io
 */
return [
    'appName' => 'Dever.io',
    'adminEmail' => 'admin@dever.io',
    'senderEmail' => 'noreply@dever.io',
    'senderName' => 'Dever.io',

    // Configurações de upload
    'maxUploadSize' => 10 * 1024 * 1024, // 10MB
    'allowedFileTypes' => ['pdf', 'png', 'jpg', 'jpeg', 'gif'],
    'allowedMimeTypes' => [
        'application/pdf',
        'image/png',
        'image/jpeg',
        'image/gif',
    ],

    // Prioridades de tarefas
    'taskPriorities' => [
        'low' => 'Baixa',
        'medium' => 'Média',
        'high' => 'Alta',
    ],

    // Status de tarefas
    'taskStatuses' => [
        'pending' => 'Pendente',
        'in_progress' => 'Em Andamento',
        'completed' => 'Concluída',
    ],
];
