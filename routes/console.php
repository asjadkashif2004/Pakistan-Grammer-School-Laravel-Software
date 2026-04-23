<?php

use App\Models\FeeCollection;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('fees:sync-automation', function () {
    FeeCollection::syncPastDueStatuses();
    $this->info('Fee automation synced (fines, payables, defaulters).');
})->purpose('Recalculate daily fines, voucher totals, and student defaulter flags');
