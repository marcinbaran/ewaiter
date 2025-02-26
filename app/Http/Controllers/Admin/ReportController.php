<?php

namespace App\Http\Controllers\Admin;

use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use App\Services\RestaurantReportService;
use Illuminate\Support\Facades\Response;

class ReportController
{
    use MultiTentantRepositoryTrait;

    public function index($restaurantId)
    {
        $restaurant = Restaurant::find($restaurantId);

        $reportService = new RestaurantReportService('pdf');
        $reportData = $reportService->generate($restaurant);

        return Response::make($reportData['report'], 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$restaurant->name.'-'.$reportData['reportNumber'].'-'.date('Y-m-d').'.pdf"',
        ]);
    }
}
