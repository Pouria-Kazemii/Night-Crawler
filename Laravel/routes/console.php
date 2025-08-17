<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:all-commands')->everyFiveMinutes();


