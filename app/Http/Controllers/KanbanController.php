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
		$task_result = DB::table('tasks')
				->select('tasks.*')
				->join('statuses', 'statuses.id', '=', 'tasks.status_id')
				->where('tasks.user_id', '=', $userId)
				->orderBy('tasks.status_id', 'asc')
				->orderBy('tasks.order', 'asc')
				->get()
				->all();

		$status_result = DB::table('statuses')
				->select('*')
				->where('user_id', '=', $userId)->orderBy('order', 'asc')->get();
		
		$tasks = array();
		foreach($status_result as $status){
			$status->tasks = [];
			foreach($task_result as $task){
				if($task->status_id == $status->id){
					
					array_push($status->tasks, $task);
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
	
	public function add_task(Request $request)
    {
		$userId = Session::get('user_id')?Session::get('user_id'):0;
        $this->validate($request, [
            'title' => ['required', 'string', 'max:56'],
            'description' => ['nullable', 'string'],
            'status_id' => ['required', 'exists:statuses,id']
		]);
		$title = $request->input('title');
		$description = $request->input('description');
		$status_id = $request->input('status_id');
		$data = array(
			'title'=>$title,
			'description'=>$description,
			'count'=>0,
			'user_id'=>$userId,
			'status_id'=> $status_id
		);

		$existingRecord = DB::table('tasks')
									->where('user_id', $userId)
									->where('title', $title)
									->where('status_id', $status_id)
									->first();
		if($existingRecord){
			return 0;
		}
		$task_id = DB::table('tasks')->updateOrInsert
		(
			[
				'user_id'=>$userId,
				'title'=>$title,
				'status_id'=> $status_id
			],
			$data
		);

		$updatedOrInsertedRecord = DB::table('tasks')
									->where('user_id', $userId)
									->where('title', $title)
									->where('status_id', $status_id)
									->first();
		$result = [];
		foreach($updatedOrInsertedRecord as $key=>$val){
			$result[$key] = $val;
		}
        return $result;
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

	public static function updateDB($data){

		$userId = Session::get('user_id')?Session::get('user_id'):0;
		$is_updated = false;
		foreach($data as $key=>$v){
			$is_update= false;
			$title = "";
			switch ($key){
				case 'number_messages':
					$title = trans('nosh.messages');
					$is_update= true;
					break;
				case 'number_scans':
					$title = trans('nosh.scanned_documents');
					$is_update= true;
					break;
				case 'number_appts':
					$title = trans('nosh.appointments_today');
					$is_update= true;
					break;
				case 'number_t_messages':
					$title = trans('nosh.telephone_messages');
					$is_update= true;
					break;
				case 'number_encounters':
					$title = trans('nosh.encounters_complete');
					$is_update= true;
					break;
				case 'number_reminders':
					$title = trans('nosh.reminders');
					$is_update= true;
					break;
				case 'number_bills':
					$title = trans('nosh.bills_process');
					$is_update= true;
					break;
				case 'number_tests':
					$title = trans('nosh.test_results_review');
					$is_update= true;
					break;
				case 'number_faxes':
					$title = trans('nosh.fax_messages');
					$is_update= true;
					break;					
				default:
					break;
			}
			if($is_update){
				$data = array(
					'title'=>$title,
					'description'=>'',
					'count'=>$v,
					'user_id'=>$userId,
				);
				DB::table('tasks')->updateOrInsert
				(
					[
						'user_id'=>$userId,
						'title'=>$title,
					],
					$data
				);
				$is_updated = true;
			}
			
		}
		if(!$is_updated){
			$arr_title = [
							trans('nosh.messages'),
							trans('nosh.scanned_documents'),
							trans('nosh.appointments_today'),
							trans('nosh.telephone_messages'),
							trans('nosh.encounters_complete'),
							trans('nosh.reminders'),
							trans('nosh.bills_process'),
							trans('nosh.test_results_review'),
							trans('nosh.fax_messages'),
			];
			foreach($arr_title as $title){
				$data = array(
					'title'=>$title,
					'description'=>'',
					'count'=>0,
					'user_id'=>$userId,
				);
				DB::table('tasks')->updateOrInsert
				(
					[
						'user_id'=>$userId,
						'title'=>$title,
					],
					$data
				);
			}
		}
	}
	
	function getTasks(){
		$userId = Session::get('user_id')?Session::get('user_id'):0;
		$task_result = DB::table('tasks')
				->select('tasks.*')
				->join('statuses', 'statuses.id', '=', 'tasks.status_id')
				->where('tasks.user_id', '=', $userId)->orderBy('tasks.status_id', 'asc')->orderBy('tasks.order', 'asc')->get();

		$status_result = DB::table('statuses')
				->select('*')
				->where('user_id', '=', $userId)->orderBy('order', 'asc')->get();
		
		$tasks = array();
		foreach($status_result as $status){
			$status->tasks = [];
			foreach($task_result as $task){
				if($task->status_id == $status->id){
					
					array_push($status->tasks, $task);
				}
				
			}
			array_push($tasks, $status);
		}
		return $tasks;
	}
}
