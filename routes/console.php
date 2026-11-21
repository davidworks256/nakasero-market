<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('database:backup')->daily()->at('02:30');
