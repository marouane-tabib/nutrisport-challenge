<?php

namespace App\Console\Commands;

use App\Services\ReportService;
use Illuminate\Console\Command;
use App\Mail\DailyReportMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Agent;
use App\Exceptions\ReportException;

class DailyReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'report:daily';

    /**
     * The console command description.
     *
     * @var string
     */    
    protected $description = 'Generate and send daily sales report for the previous day';

    /**
     * Execute the console command.
     */
    public function handle(ReportService $reportService)
    {
        $admin = Agent::findOrFail(1);

        if (!$admin->email) {
            throw ReportException::adminEmailNotFound();
        }

        $reportData = $reportService->generateDailyReport();

        Mail::to($admin->email)
            ->send(new DailyReportMail($reportData));

        $this->info('Daily report sent successfully.');

        return self::SUCCESS;
    }
}
