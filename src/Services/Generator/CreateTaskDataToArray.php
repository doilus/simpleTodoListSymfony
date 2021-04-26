<?php


namespace App\Services\Generator;


use App\Entity\Image;
use App\Repository\TaskRepository;

class CreateTaskDataToArray
{
    private TaskRepository $taskRepository;
    private array $taskArray = [[]];

    public function __construct(
        TaskRepository $taskRepository
    )
    {
        $this->taskRepository = $taskRepository;
    }

    public function putDataIntoArray(): array
    {

        $tasks = $this->getAllTask();

        $this->collectDataFromTask($tasks);

        return $this->taskArray;
    }

    private function getAllTask(): array
    {
        return $tasks = $this->taskRepository->findAll();
    }

    public function setTitle(): string
    {
        return "Tasks";
    }

    public function setHeaders(): array
    {
        return [
            "TaskId",
            "Title",
            "Slug",
            "DueDate",
            "Description",
            "UserId",
            "IsDone",
            "ImagesId"
        ];
    }

    private function collectDataFromTask(array $tasks): void
    {

        $countRow = 0;

        foreach ($tasks as $task) {
            $countColumn = 0;
            $this->taskArray[$countRow][$countColumn++] = $task->getId();
            $this->taskArray[$countRow][$countColumn++] = $task->getTitle();
            $this->taskArray[$countRow][$countColumn++] = $task->getSlug();
            $this->taskArray[$countRow][$countColumn++] = $task->getDueDate()->format("Y-m-d");
            $this->taskArray[$countRow][$countColumn++] = $task->getDescription();
            $this->taskArray[$countRow][$countColumn++] = $task->getUser()->getId();
            $this->taskArray[$countRow][$countColumn++] = $task->getisDone();

            $this->collectDataFromTaskForImages($task->getImagesId(), $countRow, $countColumn);

            $countRow++;
        }
    }

    private function collectDataFromTaskForImages(Image $imagesId, int $countRow, int $countColumn): void
    {
        foreach ($imagesId as $image) {
            $this->taskArray[$countRow][$countColumn++] = $image->getId();
        }
    }


}