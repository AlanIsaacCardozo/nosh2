<?php

namespace App\Http\Controllers;

use App;
use App\Http\Requests;
use DB;
use Illuminate\Http\Request;
use Response;
use URL;
use Session;

class KanbanController extends Controller {

	public static function index()
    {
		$userId = Session::get('user_id')?Session::get('user_id'):0;
		$query_result = DB::table('tasks')
				->select(
						'tasks.*', 
						'statuses.title as status_title', 
						'statuses.slug',
						'statuses.order as status_order',)
				->join('statuses', 'statuses.id', '=', 'tasks.status_id')
				->where('tasks.user_id', '=', $userId)->orderBy('tasks.status_id', 'asc')->orderBy('tasks.order', 'asc')->get();

		$statuses = array();
		$temptasks = json_encode($query_result);
		$newtasks = json_decode($temptasks, true);
		foreach($newtasks as $item){
			$isExisting = false;
			foreach($statuses as $status){
				if($item['status_id'] == $status['id']){
					$isExisting = true;
				}
			}
			
			if(!$isExisting){
				$status_item = array(
									'id' 	=> $item['status_id'],
									'slug' 	=> $item['slug'],
									'title' => $item['status_title'],
									'order' => $item['status_order'],
									'tasks' => []
								);
				array_push($statuses, $status_item);
			}
		}
		$tasks = array();
		foreach($statuses as $status){
			foreach($newtasks as $task){
				if($task['status_id'] == $status['id']){
					array_push($status['tasks'], $task);
				}
			}
			array_push($tasks, $status);
		}
		$result ['tasks'] = $tasks;
        return view('kanban', $result);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => ['required', 'string', 'max:56'],
            'description' => ['nullable', 'string'],
            'status_id' => ['required', 'exists:statuses,id']
        ]);

        return $request->user()
            ->tasks()
            ->create($request->only('title', 'description', 'status_id'));
    }

    public function sync(Request $request)
    {
		$userId = Session::get('user_id')?Session::get('user_id'):0;
        $this->validate(request(), [
            'columns' => ['required', 'array']
        ]);

        foreach ($request->columns as $status) {
            foreach ($status['tasks'] as $i => $task) {
				$order = $i + 1;
				echo $task['id']; 
                if ($task['status_id'] !== $status['id'] || $task['order'] !== $order) {
					DB::table('tasks')
							->where('id','=', $task['id'])
							->update([
								'status_id'=> $status['id'], 
								'order' => $order
								]);
                }
            }
		}
		$tasks = $this->getTasks();
		$result ['tasks'] = $tasks;
        return $result;
    }

    public function show(Task $task)
    {
        //
    }

    public function edit(Task $task)
    {
        //
    }

    public function update(Request $request, Task $task)
    {
        //
    }

    public function destroy(Task $task)
    {
        //
	}
	
	function getTasks(){
		$query_result = DB::table('tasks')
				->select(
						'tasks.id as id', 
						'tasks.title as title',
						'tasks.description', 
						'tasks.count',
						'tasks.order', 
						'tasks.user_id', 
						'tasks.status_id',
						'tasks.created_at',
						'tasks.updated_at', 
						'statuses.title as status_title', 
						'statuses.slug',
						'statuses.order as status_order',)
				->join('statuses', 'statuses.id', '=', 'tasks.status_id')
				->where('tasks.user_id', '=', Session::get('user_id'))->orderBy('tasks.status_id', 'asc')->orderBy('tasks.order', 'asc')->get();

		$statuses = array();
		$temptasks = json_encode($query_result);
		$newtasks = json_decode($temptasks, true);
		foreach($newtasks as $item){
			$isExisting = false;
			foreach($statuses as $status){
				if($item['status_id'] == $status['id']){
					$isExisting = true;
				}
			}
			
			if(!$isExisting){
				$status_item = array(
									'id' 	=> $item['status_id'],
									'slug' 	=> $item['slug'],
									'title' => $item['status_title'],
									'order' => $item['status_order'],
									'tasks' => []
								);
				array_push($statuses, $status_item);
			}
		}
		$tasks = array();
		foreach($statuses as $status){
			foreach($newtasks as $task){
				if($task['status_id'] == $status['id']){
					array_push($status['tasks'], $task);
				}
			}
			array_push($tasks, $status);
		}
		return $tasks;
	}

}
