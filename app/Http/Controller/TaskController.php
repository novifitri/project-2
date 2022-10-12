<?php

namespace App\Http\Controller;

use App\ContohBootcamp\Services\TaskService;
use App\Helpers\MongoModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller {
	private TaskService $taskService;
	public function __construct() {
		$this->taskService = new TaskService();
	}

	public function showTasks()
	{
		$tasks = $this->taskService->getTasks();
		return response()->json($tasks);
	}

	public function createTask(Request $request)
	{
		$request->validate([
			'title'=>'required|string|min:3',
			'description'=>'required|string'
		]);

		$data = [
			'title'=>$request->post('title'),
			'description'=>$request->post('description')
		];

		$dataSaved = [
			'title'=>$data['title'],
			'description'=>$data['description'],
			'assigned'=>null,
			'subtasks'=> [],
			'created_at'=>time()
		];

		$id = $this->taskService->addTask($dataSaved);
		$task = $this->taskService->getById($id);

		return response()->json($task);
	}


	public function updateTask($id, Request $request)
	{
		$request->validate([
			'title'=>'string',
			'description'=>'string',
			'assigned'=>'string',
			'subtasks'=>'array',
		]);

		$formData = $request->only('title', 'description', 'assigned', 'subtasks');
		$existTask = $this->taskService->getById($id);
		if(!$existTask)
		{
			return response()->json([
				"message"=> "Task ".$id." tidak ada"
			], 401);
		}

		$this->taskService->updateTask($existTask, $formData);

		$task = $this->taskService->getById($id);
	
		return response()->json($task);
	}


	// TODO: deleteTask() -> ubah sesuai service - repository pattern
	public function deleteTask($id)
	{
		$existTask = $this->taskService->getById($id);

		if(!$existTask)
		{
			return response()->json([
				"message"=> "Task ".$id." tidak ada"
			], 401);
		}

		$this->taskService->deleteTask(['_id'=>$id]);

		return response()->json([
			'message'=> 'Success delete task '.$id
		]);
	}

	// TODO: assignTask() -> ubah sesuai service - repository pattern
	public function assignTask(Request $request)
	{
		$request->validate([
			'task_id'=>'required',
			'assigned'=>'required'
		]);

		$taskId = $request->get('task_id');
		$assigned = $request->post('assigned');
		$existTask = $this->taskService->getById($taskId);

		if(!$existTask)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 401);
		}

		$existTask['assigned'] = $assigned;

		$this->taskService->updateTask($existTask, []);

		$task = $this->taskService->getById($taskId);

		return response()->json($task);
	}

	// TODO: unassignTask() -> ubah sesuai service - repository pattern
	public function unassignTask(Request $request)
	{	
		$request->validate([
			'task_id'=>'required'
		]);

		$taskId = $request->post('task_id');
		$existTask = $this->taskService->getById($taskId);

		if(!$existTask)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 401);
		}

		$existTask['assigned'] = null;

		$this->taskService->updateTask($existTask, []);

		$task =  $this->taskService->getById($taskId);

		return response()->json($task);
	}

	// TODO: createSubtask()  -> ubah sesuai service - repository pattern
	public function createSubtask(Request $request)
	{
		$request->validate([
			'task_id'=>'required',
			'title'=>'required|string',
			'description'=>'required|string'
		]);

		$taskId = $request->post('task_id');
		$title = $request->post('title');
		$description = $request->post('description');

		$existTask = $this->taskService->getById($taskId);

		if(!$existTask)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 401);
		}
		$formData = ["title"=> $title, "description" => $description];
		$id = $this->taskService->createSubtask($existTask,$formData);
		$task =  $this->taskService->getById($id);

		return response()->json($task);
	}

	// TODO deleteSubTask()
	public function deleteSubtask(Request $request)
	{
		$request->validate([
			'task_id'=>'required',
			'subtask_id'=>'required'
		]);

		$taskId = $request->post('task_id');
		$subtaskId = $request->post('subtask_id');

		$existTask = $this->taskService->getById($taskId);

		if(!$existTask)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 401);
		}
	
		$id = $this->taskService->deleteSubtask($existTask, $subtaskId);

		$task =  $this->taskService->getById($id);

		return response()->json($task);
	}

}