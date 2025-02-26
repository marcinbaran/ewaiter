<?php

namespace App\Http\Controllers\Admin;

use App\Mail\RestaurantReportMail;
use App\Models\Report;
use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use App\Services\RestaurantReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class ReportSendEmailController
{
    use MultiTentantRepositoryTrait;

    public function index(Request $request, int $restaurantId)
    {
        try {
            $report = Report::Where('restaurant_id', $restaurantId)->first();
            $reportDate = Carbon::parse($report->report_date);


            Artisan::call('cache:clear');

            if (is_null($reportDate) || $reportDate->lt(Carbon::now()->subMinutes(15))) {

                list($restaurant, $restaurantEmail) = $this->getRestaurantEmail($restaurantId);
                $reportService = new RestaurantReportService('pdf');

                $repostData = $reportService->generate($restaurant);

                $fileName = $restaurant->name . '-' . date('Y-m-d') . '.pdf';
                $filePath = 'app/reports/' . $fileName;

                $dataToSend = [
                    'To' => $restaurantEmail,
                    'title' => __('emails.report_title'),
                    'subject' => __('emails.subject'),
                    'name' => __('emails.name'),
                    'message' => __('emails.reports'),
                    'attachment' => storage_path($filePath),
                ];

                Mail::to($restaurantEmail)->send(new RestaurantReportMail($dataToSend));
                $report->report_date = Carbon::now();
                $report->save();
                $request->session()->flash('alert-success', __('admin.email_send_successfully'));

            } else {
                $request->session()->flash('alert-info', __('admin.Report To Early'));
            }
        }
        catch (\Exception $e) {
            $request->session()->flash('alert-danger', $e->getMessage());
        }
        return redirect()->route('admin.dashboard.index');
    }

    private function getRestaurantEmail(int $restaurantId): array
    {
        $restaurant = Restaurant::find($restaurantId);
        if(is_null($restaurant->manager_email)) {
            throw new \Exception(__('admin.restaurant_email_not_found'));
        }
        $reportEmail = $restaurant->manager_email;

        return array($restaurant, $reportEmail);
    }

}
