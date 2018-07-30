<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;
class employee extends Model
{
    public static function tree($id){
    return DB::select("SELECT p1.id, p1.name, p1.position, p1.hire_date, p1.salary, COUNT(p2.id) AS hasChild FROM employees p1 LEFT JOIN employees p2 ON p1.id = p2.parent_id WHERE p1.parent_id = ? GROUP BY p1.id", [$id]);
    }
    public static function get_list($page, $rqst){
    return DB::select("SELECT SQL_CALC_FOUND_ROWS p2.name as `parent_name`, p1.id, p1.parent_id, p1.name, p1.position, p1.hire_date, p1.salary FROM employees p1 LEFT JOIN employees p2 on p1.parent_id = p2.id WHERE p1.id LIKE '%".$rqst->id."%' AND p1.name LIKE '%".$rqst->name."%' and (p2.name LIKE '%".$rqst->parentName."%' || p2.name IS NULL && LENGTH('".$rqst->parentName."') = 0) AND p1.position LIKE '%".$rqst->position."%' AND (DATE(p1.hire_date) BETWEEN '".$rqst->date1."' AND '".$rqst->date2."') and p1.salary >= ".$rqst->salary1." and p1.salary <= ".$rqst->salary2." ORDER BY ".$rqst->sort." ".$rqst->sort_type." LIMIT ?, ?", [$page, $rqst->max_on_page]);
    }
    public static function create($request, $parent_id, $upload_file){
        $id = DB::table('employees')->insertGetId([
            'name' => $request->name,
            'parent_id' => $parent_id,
            'position' => $request->position,
            'hire_date' => Carbon::parse($request->hire_date)->format('Y-m-d H:i:s'),
            'salary' => $request->salary,
        ]);
        if($request->img_default == 0 && $upload_file){
            $file = $request->img;
            $file->move(public_path() . '/img/employees','employee_'.$id.'.png');
        }
    }
    public static function edit($request, $parent_id, $upload_file){
        DB::table('employees')
            ->where('id', $request->id)
            ->update([
                'name' => $request->name,
                'parent_id' => $parent_id,
                'position' => $request->position,
                'hire_date' => Carbon::parse($request->hire_date)->format('Y-m-d H:i:s'),
                'salary' => $request->salary,
        ]);
        if($request->img_default == 1 && file_exists(public_path().'/img/employees/employee_'.$request->id.'.png')){
            unlink(public_path().'/img/employees/employee_'.$request->id.'.png');
        }
        else if($request->img_default == 0 && $upload_file){
            $file = $request->img;
            $file->move(public_path() . '/img/employees','employee_'.$request->id.'.png');
        }
    }
    public static function _delete($id, $parent_id){
        DB::table('employees')
            ->where('parent_id', $id)
            ->update([
                'parent_id' => $parent_id,
        ]);
        DB::table('employees')->where('id', $id)->delete();
        if(file_exists(public_path().'/img/employees/employee_'.$id.'.png')){
            unlink(public_path().'/img/employees/employee_'.$id.'.png');
        }
    }
    public static function employee_data($id){
    return DB::table('employees')->where('id', $id)->get();
    }
    public static function count(){
    return DB::table('employees')->count();
    }
    public static function data_count(){
    return DB::select('SELECT FOUND_ROWS() as count');
    }
    public static function max_salary(){
    return DB::table('employees')->max('salary');
    }
}