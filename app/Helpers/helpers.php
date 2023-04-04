<?php

function user_storage_percentage($used, $user, $additionals = null)
{
//    dd($used,$user,$additionals,$user->currentPlan());
        $capacity = $user->currentPlan()?$user->currentPlan()->storage_capacity:1;

    if ($additionals) {
        $addUsed = $used + $additionals;
    }
    // Format gigabytes to bytes
    $total = $capacity * pow(1024, 3);

    // Count progress
    $progress = ($addUsed * 100) / $total;

    // Return in 2 decimal
    return number_format((float)$progress, 2, '.', '');
}

function formatBytes($size, $unit = "")
{
    if ((!$unit && $size >= 1 << 30) || $unit == "GB")
        return number_format($size / (1 << 30), 2) . "GB";
    if ((!$unit && $size >= 1 << 20) || $unit == "MB")
        return number_format($size / (1 << 20), 2) . "MB";
    if ((!$unit && $size >= 1 << 10) || $unit == "KB")
        return number_format($size / (1 << 10), 2) . "KB";
    return number_format($size) . " bytes";
}