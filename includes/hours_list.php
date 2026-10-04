<?php
$openingHours ??= fetch_opening_hours();
?>
<dl class="hours-list mb-0">
<?php for ($weekday = 1; $weekday <= 7; $weekday++): ?>
    <dt><?= e(ucfirst(HU_WEEKDAYS[$weekday - 1])) ?></dt>
    <dd><?= isset($openingHours[$weekday])
        ? e(format_time($openingHours[$weekday]['open_time']) . ' – ' . format_time($openingHours[$weekday]['close_time']))
        : 'Zárva' ?></dd>
<?php endfor; ?>
</dl>
