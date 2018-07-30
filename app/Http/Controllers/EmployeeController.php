<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Employee;
use DB;
use Validator;
use Carbon\Carbon;
class EmployeeController extends Controller
{
    public function index()
    {
        return view('index');
    }
    public function tree(Request $request)
    {
        $count =  Employee::count();
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1|max:'.$count
        ]);
        if ($validator->fails()) {
        return view('tree', ['tree' => Employee::tree(0), 'id' => 0]);
        }
        return view('tree', ['tree' => Employee::tree($request->id), 'id' => $request->id]);
    }
    public function get_employee_data(Request $request){
        $this->validate($request, [
        'id' => 'required|integer|min:1'
        ]);
        return \Response::json(Employee::employee_data($request->id));
    }
    public function ajax_D_employee(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1'
        ]);
        if ($validator->fails()) {
            return \Response::json(
                    array(
                        array(
                        'status' => 'error',
                        'reason' => 'Unknown',
                        'msg' => 'An unknown error occured. Please reload the page and try again.',
                        )
                    )
            );
        }
        else{
        $parent_id = DB::table('employees')->select('parent_id')->where('id', $request->id)->get();
            Employee::_delete($request->id, $parent_id[0]->parent_id);
            return \Response::json(
                    array(
                        array(
                        'status' => 'success',
                        'reason' => 'delete',
                        'msg' => 'Profile has been successfully deleted.',
                        )
                    )
            );
        }
    }
    public function ajax_CE_employee(Request $request)
    {
        $response = array();
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:0',
            'img' => 'required|image|mimes:png|max:5000|dimensions:min_width=24,min_height=24,max_width=400,max_height=400',
            'img_default' => 'required|integer|min:0|max:1',
            'name' => 'required|alpha_dash|min:1',
            'parent_name' => 'required|string|min:1',
            'salary' => 'required|integer|min:0',
            'position' => 'required|string|min:1',
            'hire_date' => 'required|date',
        ]);
        if ($validator->fails()) {
            $failedRules = $validator->failed();
            if(isset($failedRules['img_default']) || isset($failedRules['id'])){
            return \Response::json(
                    array(
                        array(
                        'status' => 'error',
                        'reason' => 'Unknown',
                        'msg' => 'An unknown error occured. Please reload the page and try again.',
                        )
                    )
                );
            }
            $response = array(
                array(
                    'status' => isset($failedRules['name']) ? 'error' : 'success',
                    'reason' => 'Name'
                ),
                array(
                    'status' => (isset($failedRules['parent_name']) && $request->parent_name != NULL) || $this->parent_search($request) === FALSE ? 'error' : 'success',
                    'reason' => 'ParentName'
                ),
                array(
                    'status' => isset($failedRules['salary']) ? 'error' : 'success',
                    'reason' => 'Salary'
                ),
                array(
                    'status' => isset($failedRules['position']) ? 'error' : 'success',
                    'reason' => 'Position'
                ),
                array(
                    'status' => isset($failedRules['hire_date']) ? 'error' : 'success',
                    'reason' => 'Date'
                ),
                array(
                    'status' => isset($failedRules['img']) && $request->img_default == 0 && $request->id == 0 ? 'error' : 'success',
                    'reason' => 'File'
                )
            );
            $upload_file = isset($failedRules['img']) ? false : true;
            if ($this->error_search($response) === FALSE){
                if($request->id == 0){
                    Employee::create($request, $this->parent_search($request), $upload_file);
                    return \Response::json(
                        array(
                            array(
                               'status' => 'success',
                                'reason' => 'create',
                                'msg' => 'Employee successfully added to the database!'
                            )
                        )
                    );
                }
                else{
                    Employee::edit($request, $this->parent_search($request), $upload_file);
                    return \Response::json(
                        array(
                            array(
                                'status' => 'success',
                                'reason' => 'edit',
                                'msg' => 'Edit success!'
                            )
                        )
                    );
                }
            }
            else{
                return \Response::json($response);
            }
        }
        else{
        $parent_id = $this->parent_search($request);
        if($request->id == 0){
                    Employee::create($request, $this->parent_search($request), true);
                    return \Response::json(
                        array(
                            array(
                               'status' => 'success',
                                'reason' => 'create',
                                'msg' => 'Employee successfully added to the database!'
                            )
                        )
                    );
                }
                else{
                    Employee::edit($request, $this->parent_search($request), true);
                    return \Response::json(
                        array(
                            array(
                                'status' => 'success',
                                'reason' => 'edit',
                                'msg' => 'Edit success!'
                            )
                        )
                    );
                }
        }
    }
    private function error_search($response){
        foreach ($response as $item){
            if(array_search('error', $item) !== FALSE){
                return TRUE;
            }
        }
        return FALSE;
    }
    private function parent_search($request){
        if($request->parent_name == NULL){
            return 0;
        }
        $parent_id = preg_replace('/[^0-9]/', '', stristr($request->parent_name, '(ID: '));
        if(strlen($parent_id) == 0){
           $search_parent_id = DB::table('employees')->select('id')->where('name', $request->parent_name)->get();
        }
        else{
           $search_parent_id = DB::table('employees')->select('id')->where('id', $parent_id)->get();
        }
        if(count($search_parent_id) == 0){
            return FALSE;
        }
        return $search_parent_id[0]->id;
    }
    public function ajax_search_employee(Request $request)
    {
        $this->validate($request, [
        'value' => 'required|string|min:1',
        ]);
        $list = DB::table('employees')->select('id', 'name')->where('name', 'like', '%' . $request->value . '%')->limit(5)->get();
        $arr = array();
        $default_img_path = 'http://'.$_SERVER['HTTP_HOST'].'/img/employees/employee_default.png';
        foreach ($list as $employee){
            $img_path = 'http://'.$_SERVER['HTTP_HOST'].'/img/employees/employee_'.$employee->id.'.png';
            $path = file_exists(public_path().'/img/employees/employee_'.$employee->id.'.png') ? $img_path : $default_img_path;
            $arr[] = array('img_path' => $path, 'id' => $employee->id, 'name' => $employee->name);
        }
        return \Response::json($arr);
    }
    public function change_parent_id(Request $request)
    {
        $this->validate($request, [
        'employee_id' => 'required|integer|min:1',
        'new_parent_id' => 'required|integer|min:0',
        ]);
        DB::table('employees')->where('id', $request->employee_id)->update(['parent_id' => $request->new_parent_id]);
        $response = array(
            'status' => 'success',
            'msg' => 'Setting created successfully',
        );
        return \Response::json($response);
    }
    public function list(Request $request){
        $count =  Employee::count();
        $max_salary = Employee::max_salary();
        $validator = Validator::make($request->all(), [
            'page' => 'required|integer|min:1',
            'id' => 'required|integer|min:1',
            'name' => 'required|string|min:1',
            'parentName' => 'required|string|min:1',
            'position' => 'required|string|min:1',
            'date1' => 'required|date',
            'date2' => 'required|date',
            'max_on_page' => 'required|integer|min:5|max:100',
            'salary1' => 'required|integer|min:0|max:'.$max_salary,
            'salary2' => 'required|integer|min:0|max:'.$max_salary,
            'sort' => 'required|integer|min:0|max:5',
            'sort_type' => 'required|integer|min:0|max:1',
        ]);
        $rqst = array();
        $sort_arr = ['id', 'name', 'position', 'hire_date', 'salary', 'parent_name'];
        $sort_type_arr = ['ASC', 'DESC'];
        if ($validator->fails()) {
            $failedRules = $validator->failed();
            $rqst = (object) array (
                'id' => !isset($failedRules['id']) ? $request->id : "",
                'name' => !isset($failedRules['name']) ? $request->name : "",
                'parentName' => !isset($failedRules['parentName']) ? $request->parentName : "",
                'position' => !isset($failedRules['position']) ? $request->position : "",
                'date1' => !isset($failedRules['date1']) ? Carbon::parse($request->date1)->format('Y-m-d H:i:s') : "2000-01-01 00:00:00",
                'date2' => !isset($failedRules['date2']) ? Carbon::parse($request->date2)->format('Y-m-d H:i:s') : "2099-01-01 00:00:00",
                'salary1' => !isset($failedRules['salary1']) ? $request->salary1 : 0,
                'salary2' => !isset($failedRules['salary2']) ? $request->salary2 : $max_salary,
                'sort' => !isset($failedRules['sort']) ? $sort_arr[$request->sort] : $sort_arr[1],
                'sort_type' => !isset($failedRules['sort_type']) ? $sort_type_arr[$request->sort_type] : $sort_type_arr[0],
                'max_on_page' => !isset($failedRules['max_on_page']) ? $request->max_on_page : 5
            );
            if(isset($failedRules['page'])) {
            return view('list', ['page' => 1, 'list' => Employee::get_list(0, $rqst), 'max_salary' => $max_salary, 'count' => Employee::data_count()[0]->count, 'max_on_page' => $rqst->max_on_page]);
            }
        }
        else{
            $rqst = (object) array (
                'id' => $request->id,
                'name' => $request->name,
                'parentName' => $request->parentName,
                'position' => $request->position,
                'date1' => Carbon::parse($request->date1)->format('Y-m-d H:i:s'),
                'date2' => Carbon::parse($request->date2)->format('Y-m-d H:i:s'),
                'salary1' => $request->salary1,
                'salary2' => $request->salary2,
                'sort' => $sort_arr[$request->sort],
                'sort_type' => $sort_type_arr[$request->sort_type],
                'max_on_page' => $request->max_on_page
            );
        }
        return view('list', ['page' => $request->page, 'list' => Employee::get_list(($request->page - 1)*$request->max_on_page, $rqst, $request->max_on_page), 'max_salary' => $max_salary, 'count' => Employee::data_count()[0]->count, 'max_on_page' => $request->max_on_page]);
    }
}
