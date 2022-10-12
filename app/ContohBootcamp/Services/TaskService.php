<?php

namespace App\ContohBootcamp\Services;

use App\ContohBootcamp\Repositories\TaskRepository;

class TaskService {
	private TaskRepository $taskRepository;

	public function __construct() {
		$this->taskRepository = new TaskRepository();
	}

	/**
	 * NOTE: untuk mengambil semua tasks di collection task
	 */
	public function getTasks()
	{
		$tasks = $this->taskRepository->getAll();
		return $tasks;
	}

	/**
	 * NOTE: menambahkan task
	 */
	public function addTask(array $data)
	{
		$taskId = $this->taskRepository->create($data);
		return $taskId;
	}

	/**
	 * NOTE: UNTUK mengambil data task
	 */
	public function getById(string $taskId)
	{
		$task = $this->taskRepository->getById($taskId);
		return $task;
	}

	/**
	 * NOTE: untuk update task
	 */
	public function updateTask(array $editTask, array $formData)
	{
		if(isset($formData['title']))
		{
			$editTask['title'] = $formData['title'];
		}

		if(isset($formData['description']))
		{
			$editTask['description'] = $formData['description'];
		}

		$id = $this->taskRepository->save($editTask);
		return $id;
	}

	/**
	 * NOTE : untuk delete task
	 */
	public function deleteTask(array $data)
	{
		$id = $this->taskRepository->delete($data);
		return $id;
	}

	/**
	 * NOTE : untuk createSubtask
	 */
	public function createSubtask(array $existTask, array $formData)
	{
		$subtasks = isset($existTask['subtasks']) ? $existTask['subtasks'] : [];

		$subtasks[] = [
			'_id'=> (string) new \MongoDB\BSON\ObjectId(),
			'title'=>$formData["title"],
			'description'=>$formData["description"]
		];

		$existTask['subtasks'] = $subtasks;

		$id = $this->taskRepository->save($existTask);
		return $id;
	}

	/**
	 * NOTE : untuk deleteSubtask
	 */
	public function deleteSubtask(array $existTask, $subtaskId)
	{
		$subtasks = isset($existTask['subtasks']) ? $existTask['subtasks'] : [];
		// Pencarian dan penghapusan subtask
		$subtasks = array_filter($subtasks, function($subtask) use($subtaskId) {
			if($subtask['_id'] == $subtaskId)
			{
				return false;
			} else {
				return true;
			}
		});
		$subtasks = array_values($subtasks);
		$existTask['subtasks'] = $subtasks;

		$id = $this->taskRepository->save($existTask);

		return $id;
	}
}