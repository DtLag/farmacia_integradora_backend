<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UserMetricsRequest;

class ReportController extends Controller
{

use ApiResponse;

public function salesReport(ReportRequest $request){

    
    $period = $request->input('period');
    
    if($period === 'week') {

        $endDate = now()->format('Y-m-d');
        $startDate = now()->subWeek()->format('Y-m-d');
        $report= DB::select('CALL sp_sales_report_by_dates (?,?)',
        [
            $startDate.' 00:00:00', 
            $endDate.' 23:59:59'
        ]);


    } elseif ($period === 'month') {

        $endDate = now()->format('Y-m-d');
        Log::info('Fecha de fin: ' . $endDate);
        $startDate = now()->subMonth()->format('Y-m-d');
        Log::info('Fecha de inicio: ' . $startDate);
        $report= DB::select('CALL sp_sales_report_by_dates (?,?)',
        [
            $startDate.' 00:00:00', 
            $endDate.' 23:59:59'
        ]);
        
    } elseif ($period === 'custom') {

        $startDate = $request->input('from');
        $endDate = $request->input('to');
        $report= collect(DB::select('CALL sp_sales_report_by_dates (?,?)',
        [
            $startDate . ' 00:00:00', 
            $endDate. ' 23:59:59'
        ]));

    
    } else {
        return $this->response(false, 'Período no válido', null, null, 400);
    }

    return $this->response(true, 'Reporte generado correctamente ' , $report,null, 200);

}

public function inventoryReport(ReportRequest $request){
    
    $period= $request->input('period');

    if($period === 'week') {

        $endDate = now()->format('Y-m-d');
        $startDate = now()->subWeek()->format('Y-m-d');
        $report= DB::select('CALL sp_product_rotation_by_dates (?,?)',
        [
            $startDate.' 00:00:00', 
            $endDate.' 23:59:59'
        ]);
    }
    elseif ($period === 'month') {

        $endDate = now()->format('Y-m-d');
        
        $startDate = now()->subMonth()->format('Y-m-d');
    
        $report= DB::select('CALL sp_product_rotation_by_dates (?,?)',
        [
            $startDate.' 00:00:00', 
            $endDate.' 23:59:59'
        ]);
        
    } elseif ($period === 'custom') {

        $startDate = $request->input('from');
        $endDate = $request->input('to');
        $report= collect(DB::select('CALL sp_product_rotation_by_dates (?,?)',
        [
            $startDate . ' 00:00:00', 
            $endDate. ' 23:59:59'
        ]));

    
    } else {
        return $this->response(false, 'Período no válido', null, null, 400);
    }
    return $this->response(true, 'Reporte generado correctamente ' , $report,null, 200);

}
public function user_metrics(UserMetricsRequest $request){

    $user = $request->query('user');
    $period = $request->query('period');

    if($period === 'week') {

        $endDate = now()->format('Y-m-d');
        $startDate = now()->subWeek()->format('Y-m-d');

        $metrics = DB::select('CALL sp_user_metrics_by_name(?,?,?)', [
        $user,
        $startDate . ' 00:00:00',
        $endDate . ' 23:59:59',
    ]);

    } elseif ($period === 'month') {

        $endDate = now()->format('Y-m-d');
        $startDate = now()->subMonth()->format('Y-m-d');

        $metrics = DB::select('CALL sp_user_metrics_by_name(?,?,?)', [
        $user,
        $startDate . ' 00:00:00',
        $endDate . ' 23:59:59',
    ]);
    } elseif ($period === 'custom') {

        $startDate = $request->query('from');
        $endDate = $request->query('to');

        $metrics = DB::select('CALL sp_user_metrics_by_name(?,?,?)', [
        $user,
        $startDate . ' 00:00:00',
        $endDate . ' 23:59:59',
    ]);
    } else {
        return $this->response(false, 'Período no válido', null, null, 400);
    }

    return $this->response(true, 'Métricas de usuario obtenidas correctamente', $metrics, null, 200);
}
}
