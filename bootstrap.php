<?php

use think\Console;

if (class_exists(Console::class)) {
    Console::addDefaultCommands([
        \ke\AutoValidate::class
    ]);
}
