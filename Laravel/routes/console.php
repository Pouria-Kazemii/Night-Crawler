<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:check-nodes')->everyMinute();
Schedule::command('app:check-crawler-schedule')->everyMinute();
// Schedule::command('app:check-running-job')->everyFiveMinutes();
