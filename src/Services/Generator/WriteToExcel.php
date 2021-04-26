<?php


namespace App\Services\Generator;


use App\Repository\TaskRepository;

class WriteToExcel
{

    private TaskRepository $taskRepository;

    public function __construct(
        TaskRepository $taskRepository
    )
    {
        $this->taskRepository = $taskRepository;
    }

}