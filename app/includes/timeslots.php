<?php
/**
 * Generates hourly time-slot options from 9:00 AM to 12:00 AM (midnight).
 * Each slot is one hour long.
 */
function generateTimeSlots(int $startHour = 9, int $endHour = 24): array
{
  $slots = [];
  for ($h = $startHour; $h < $endHour; $h++) {
    $start = sprintf('%02d:00', $h);
    $end = sprintf('%02d:00', $h + 1 === 24 ? 0 : $h + 1);
    $labelStart = date('g:i A', strtotime($start));
    $labelEnd = ($h + 1 === 24) ? '12:00 AM' : date('g:i A', strtotime($end));
    $slots[] = [
      'start' => $start,
      'end' => $end,
      'label' => "$labelStart – $labelEnd",
    ];
  }
  return $slots;
}
