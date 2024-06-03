<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChangeLogs;
use App\Models\Permission;
use App\Models\RolesAndPermissions;
use App\Models\UsersAndRoles;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class LogsController extends Controller
{
    public function createLogs($table_name, $action_name, $row_id, $value_before, $value_after, $user_id)
    {
        ChangeLogs::create([
            'table_name' => $table_name,
            'action_name' => $action_name,
            'row_id' => $row_id,
            'value_before' => $value_before,
            'value_after' => $value_after,
            'created_by' => $user_id,
        ]);
    }

    public function getUserLogs(Request $request)
    {
        $id = $request->id;
        $Logs = ChangeLogs::where('created_by', $id)->get();
        return $Logs;
    }


    public function getRoleLogs(Request $request)
    {
        $id = $request->id;
        $LogsRole = ChangeLogs::where('table_name', 'Roles')->where('row_id', $id)->get();

        $temp1 = ChangeLogs::where('table_name', 'UsersAndRoles')->get();
        $LogsUsersAndRoles = [];
        foreach ($temp1 as $log) {
            $t = UsersAndRoles::where('id', $log->row_id)->first();
            try {
                if ($t->role_id == $id) {
                    array_push($LogsUsersAndRoles, $log);
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        $temp2 = ChangeLogs::where('table_name', 'RolesAndPermission')->get();
        $LogsRolesAndPermissions = [];
        foreach ($temp2 as $log) {
            $t = RolesAndPermissions::where('id', $log->row_id)->first();
            try {
                if ($t->role_id == $id) {
                    array_push($LogsRolesAndPermissions, $log);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        $Logs = $LogsRole->concat($LogsUsersAndRoles)->concat($LogsRolesAndPermissions);
        return $Logs;
    }

    public function getPermissionLogs(Request $request)
    {
        $id = $request->id;
        $LogsRole = ChangeLogs::where('table_name', 'permissions')->where('row_id', $id)->get();
        $temp = ChangeLogs::where('table_name', 'rolesAndPermissions')->get();
        $LogsRolesAndPermissions = [];
        foreach ($temp as $log) {
            $t = RolesAndPermissions::where('id', $log->row_id)->first();
            if ($t->permission_id == $id) {
                array_push($LogsRolesAndPermissions, $log);
            }
        }

        $Logs = $LogsRole->concat($LogsRolesAndPermissions);
        return $Logs;
    }

    public function restoreRow(Request $request)
    {
        $log_id = $request->id;
        $user = $request->user();

        DB::beginTransaction();

        try {
            $log = ChangeLogs::where('id', $log_id)->first();

            $table = $log->table_name;
            $curent_value = $log->value_after;
            $prev_value = $log->value_before;

            if ($prev_value == 'null') {
                DB::table($table)->where('id', $log->row_id)->delete();
                $this->createLogs($table, __FUNCTION__, $log->row_id, $curent_value, 'null', $user->id);
            } else if ($curent_value == "null") {
                $dataArray = json_decode($prev_value, true);
                $dataArray['created_at'] = Carbon::parse($dataArray['created_at'])->format('Y-m-d H:i:s');
                $dataArray['updated_at'] = Carbon::parse($dataArray['updated_at'])->format('Y-m-d H:i:s');
                if (DB::table($table)->where('id', $dataArray['id'])->exists()) {
                    DB::table($table)->where('id', $dataArray['id'])->delete();
                }
                DB::table($table)->insert($dataArray);
                $this->createLogs($table, __FUNCTION__, $log->row_id, 'null', $curent_value, $user->id);
            } else {
                $dataArray = json_decode($prev_value, true);
                $dataArray['created_at'] = Carbon::parse($dataArray['created_at'])->format('Y-m-d H:i:s');
                $dataArray['updated_at'] = Carbon::parse($dataArray['updated_at'])->format('Y-m-d H:i:s');
                DB::table($table)->where('id', $log->row_id)->update($dataArray);
                $this->createLogs($table, __FUNCTION__, $log->row_id, $curent_value, $prev_value, $user->id);
            }

            DB::commit();

            return response()->json(['status' => '200']);
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
