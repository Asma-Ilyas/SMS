@php
    $key = strtolower((string) $status);
    $green  = 'bg-green-100 text-green-700';
    $red    = 'bg-red-100 text-red-700';
    $yellow = 'bg-yellow-100 text-yellow-700';
    $blue   = 'bg-blue-100 text-blue-700';
    $indigo = 'bg-indigo-100 text-indigo-700';
    $gray   = 'bg-gray-100 text-gray-600';
    $map = [
        'present'=>$green,'paid'=>$green,'active'=>$green,'approved'=>$green,'completed'=>$green,'available'=>$green,'pass'=>$green,
        'absent'=>$red,'overdue'=>$red,'fail'=>$red,'cancelled'=>$red,'rejected'=>$red,
        'late'=>$yellow,'pending'=>$yellow,'partial'=>$yellow,'half_day'=>$yellow,
        'upcoming'=>$blue,'leave'=>$blue,'excused'=>$blue,
        'ongoing'=>$indigo,'on_duty'=>$indigo,
        'holiday'=>$gray,'vacated'=>$gray,'inactive'=>$gray,
    ];
@endphp
<span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $map[$key] ?? $gray }}">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
