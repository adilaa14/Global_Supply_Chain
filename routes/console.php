<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Console\ClosureCommand;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

// Weather Sync
Schedule::command('weather:sync')->everyFifteenMinutes()->withoutOverlapping();

// Currency Sync
Schedule::command('currency:sync')->hourly()->withoutOverlapping();

// News Sync
Schedule::command('news:sync')->everyThirtyMinutes()->withoutOverlapping();

// Risk Calculation Engine
Schedule::command('risk:calculate')->everyTenMinutes()->withoutOverlapping();

// Analytics Update
Schedule::command('analytics:generate')->dailyAt('00:00')->withoutOverlapping();
