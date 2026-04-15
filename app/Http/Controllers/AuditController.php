<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    use ApiResponse;

    public function index(Request $request){
        $query = Audit::query()->with('user.role');

        $start = $request->query('startDate');
        $end = $request->query('endDate');

        $user = $request->query('user');
        $module = $request->query('module');

        if($start){
            $startDate = Carbon::parse($start)->startOfDay();

            $query->where('date_time', '>=', $startDate);
        }

        if($end){
            $endDate = Carbon::parse($end)->endOfDay();

            $query->where('date_time', '<=', $endDate);
        }

        if($user){
            $query->where('user_id', $user);
        }

        if($module){
            $query->where('affected_module', $module);
        }

        $audits = $query->get();

        return $this->response(true, 'Auditorias encontradas y filtradas', $audits, null, 200);
    }

    public function todayAudits(){
        $query = Audit::query();

        $hoy = Carbon::today()->startOfDay();

        $today = $query->where('date_time', $hoy)->get();

        return $this->response(true, 'Auditorías de hoy', $today, null, 200);
    }
}
