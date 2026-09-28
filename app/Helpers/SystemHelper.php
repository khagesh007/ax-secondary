<?php

function getClassFileNames(): array
{
    $path = app_path('Events') . '/*.php';
    return collect(glob($path))->map(fn ($file) => basename($file, '.php'))->toArray();
}

function globalDate($date)
{
    return date('d-m-Y', strtotime($date));
}
function globalDateTime($date)
{

    return date('d-m-Y h:i A', strtotime($date));
}
