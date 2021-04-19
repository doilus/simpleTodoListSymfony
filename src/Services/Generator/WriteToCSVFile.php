<?php


namespace App\Services\Generator;


use App\Repository\TaskRepository;

class WriteToCSVFile
{
    private TaskRepository $taskRepository;

    public function __construct(
        TaskRepository $taskRepository
    )
    {
        $this->taskRepository = $taskRepository;
    }

    public function writeToCSV(): void
    {
        $tasks = $this->taskRepository->findAll();

        $fp = fopen("sample.csv", "w");
        foreach ($tasks as $task) {
            $data = [
                $task->getId(),
                $task->getTitle(),
                $task->getSlug(),
                $task->getDueDate()->format('Y-m-d H:i:s'),
                $task->getUser()->getId(),
                $task->getIsDone() ? "True" : "False",
                $task->getDescription()];
            fputcsv($fp, $data, ',');
        }
        fclose($fp);
    }
}