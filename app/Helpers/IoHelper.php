<?php



//=========

function ioRouteResource($name, $controller, $except = []) {
    \Illuminate\Support\Facades\Route::resource($name, $controller)->except($except);
    \Illuminate\Support\Facades\Route::prefix($name)->name("$name.")->group(function () use ($controller, $name, $except) {
        if (!in_array('search', $except)) \Illuminate\Support\Facades\Route::post('/search', [$controller, 'search'])->name('search');
        if (!in_array('restore', $except)) \Illuminate\Support\Facades\Route::put('/{'. (last(explode('/', $name))) . '}/restore', [$controller, 'restore'])->name('restore');
    });
}

function ioRouteResourceApi($name, $controller) {
    \Illuminate\Support\Facades\Route::resource($name, $controller)->except(['index', 'create', 'edit']);
    \Illuminate\Support\Facades\Route::prefix($name)->name("$name.")->group(function () use ($controller, $name) {
        \Illuminate\Support\Facades\Route::get('/', [$controller, 'index'])->name('index');
        \Illuminate\Support\Facades\Route::put('/{'. $name . '}/restore', [$controller, 'restore'])->name('restore');
    });
}

function arrayNumber($max, $min = 1, $leadingZero = false, $suffix = '')
{
    $result = [];
    for ($i = $min; $i <= $max; $i++) {
        $number = $i;
        if ($leadingZero == true && strlen($number) == 1) $number = '0' . $number;
        $result[$number] = $number . \Illuminate\Support\Str::plural($suffix, $number);
    }
    return $result;
}

function incrementWorkday($start, $daysToIncrement, array $holidays = [])
{
    $date = \Carbon\Carbon::parse($start);
    $currentDate = $date->copy();
    $incrementedDate = $currentDate->copy();
    while ($daysToIncrement > 0) {
        $incrementedDate->addDay();
        if ($incrementedDate->isWeekend() || in_array($incrementedDate->toDateString(), $holidays)) continue;

        $daysToIncrement--;
    }

    return $incrementedDate->toDateString();
}

function hasRoute($route, $params = [])
{
    return (\Illuminate\Support\Facades\Route::has($route)) ? route($route, $params) : '#';
}

function paginateOptions()
{
    $result = [];
    foreach ([10, 20, 50, 100] as $value) $result[$value] = $value;
    return $result;
}

function genders()
{
    return ['L' => 'Laki-laki', 'P' => 'Perempuan'];
}

function religions()
{
    return [
        'Islam',
        'Katolik',
        'Kristen',
        'Hindu',
        'Budha',
        'Konghucu',
    ];
}

function citizenships()
{
    return [
        'WNI', 'WNA'
    ];
}

function strLimit($value, $limit = 60)
{
    return \Illuminate\Support\Str::limit($value, $limit);
}

function strSlug($value, $separator = '-')
{
    return \Illuminate\Support\Str::slug($value, $separator);
}

function strUnslug($value, $separator = '-')
{
    return ucwords(strtolower(str_replace($separator, ' ', $value)));
}

function strPlural($value, $count = 1)
{
    if ($count === 0) $count = 1;
    return \Illuminate\Support\Str::plural($value, $count);
}

function removeSpace($value)
{
    return str_replace(' ', '', $value);
}

function serializeArray($data)
{
    return http_build_query($data);
}

function formatNumber($number, $currency = 'IDR')
{
    return $number ? ($currency == 'IDR' ? number_format($number, 0, ',', '.') : number_format($number, 2, '.', ',')) : '0';
}

function formatDecimal($number, $decimal = 2)
{
    return $number ? number_format($number, $decimal, ',', '.') : '';
}

function formatDecimal2($number)
{
    return $number ? number_format($number, 4, ',', '.') : '';
}

function listDates($start_date, $end_date, $format = 'Y-m-d') {
    $dates = [];

    $start = new \DateTime($start_date);
    $end = new \DateTime($end_date);
    $end->modify('+1 day');
    $interval = new \DateInterval('P1D');
    $period = new \DatePeriod($start, $interval, $end);
    foreach ($period as $date) $dates[] = $date->format($format);

    return $dates;
}

function listHours($start_time, $end_time, $format = 'H:i:s') {
    $hours = [];

    $start = new \DateTime($start_time);
    $end = new \DateTime($end_time);
    $end->modify('+1 hour'); // include the end time
    $interval = new \DateInterval('PT1H'); // 1 hour interval
    $period = new \DatePeriod($start, $interval, $end);

    foreach ($period as $time) {
        $hours[] = $time->format($format);
    }

    return $hours;
}


function months($short = false)
{
    return $short ?
        array('Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des') :
        array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
}

function days()
{
    return array('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu');
}

function fulldate($date, $divider = "", $shortMonth = false)
{
    if ($date == "") return "";

    $dayText = days();
    $monthText = months($shortMonth);

    $dayInt = date('N', strtotime($date));
    $date = explode("-", date('Y-m-d', strtotime($date)));
    $monthInt = (int)$date[1];

    $result = [];
    if ($divider !== "") {
        $result[] = $dayText[$dayInt - 1] . ', ';
    }
    $result[] = $date[2];
    $result[] = ' ';
    $result[] = $monthText[$monthInt - 1];
    $result[] = ' ';
    $result[] = $date[0];

    return implode($divider, $result);
}


function formatDate($date, $divider = "-")
{
    return $date ? implode($divider, array_reverse(explode("-", date('Y-m-d', strtotime($date))))) : '';
}

function formatTime($time, $short = true)
{
    return $time ? ($short ? date('H:i', strtotime($time)) : date('H:i:s', strtotime($time))) : '';
}

function calculateAge($birthDate) {
    try {
        $birth = new DateTime($birthDate);
        $today = new DateTime('today');
        return $birth->diff($today)->y;
    } catch (\Exception $e) {
        return '-';
    }
}

function numberToAlphabeth($number)
{
    return chr(64 + $number);
}

function numberToRoman($number)
{
    $map = [
        'M' => 1000, 'CM' => 900,
        'D' => 500, 'CD' => 400,
        'C' => 100, 'XC' => 90,
        'L' => 50, 'XL' => 40,
        'X' => 10, 'IX' => 9,
        'V' => 5, 'IV' => 4,
        'I' => 1,
    ];

    $result = '';
    foreach ($map as $roman => $int) {
        while ($number >= $int) {
            $result .= $roman;
            $number -= $int;
        }
    }
    return $result;
}


function romanToNumber($roman)
{
    $romans = [
        'M' => 1000, 'CM' => 900,
        'D' => 500, 'CD' => 400,
        'C' => 100, 'XC' => 90,
        'L' => 50, 'XL' => 40,
        'X' => 10, 'IX' => 9,
        'V' => 5, 'IV' => 4,
        'I' => 1,
    ];

    $result = 0;
    foreach ($romans as $key => $value) {
        while (strpos($roman, $key) === 0) {
            $result += $value;
            $roman = substr($roman, strlen($key));
        }
    }
    return $result;
}


function spellNumberCore($nilai) {
    $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    if ($nilai < 12) return $huruf[$nilai];
    elseif ($nilai < 20) return spellNumberCore($nilai - 10) . " belas";
    elseif ($nilai < 100) return spellNumberCore($nilai / 10) . " puluh " . spellNumberCore($nilai % 10);
    elseif ($nilai < 1000) return spellNumberCore($nilai / 100) . " ratus " . spellNumberCore($nilai % 100);
    elseif ($nilai < 1000000) return spellNumberCore($nilai / 1000) . " ribu " . spellNumberCore($nilai % 1000);
    elseif ($nilai < 1000000000) return spellNumberCore($nilai / 1000000) . " juta " . spellNumberCore($nilai % 1000000);
    elseif ($nilai < 1000000000000) return spellNumberCore($nilai / 1000000000) . " milyar " . spellNumberCore(fmod($nilai, 1000000000));
    elseif ($nilai < 1000000000000000) return spellNumberCore($nilai / 1000000000000) . " trilyun " . spellNumberCore(fmod($nilai, 1000000000000));
    return "";
}

function spellNumber($number) {
    if ($number == '') return "";
    if ($number == 0) return "nol";
    elseif ($number < 0) return "minus " . spellNumberCore(abs($number));
    else return trim(spellNumberCore($number));
}

function dateDifference($date1, $date2)
{
    return (new DateTime($date2))->diff(new DateTime($date1))->days + 1;
}

function unformatDate($date)
{
    return $date ? date('Y-m-d', strtotime($date)) : null;
}

function unformatTime($date)
{
    return $date ? date('H:i:s', strtotime($date)) : null;
}

function unformatNumber($number)
{
    if ($number == '') return $number;
    $number = str_replace('.', '', $number);
    $number = str_replace(',', '.', $number);
    return $number;
}

function randomColor() {
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}

function softenColor($hex, $amount = 0.8) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) == 3) $hex = $hex[0].$hex[0] . $hex[1].$hex[1] . $hex[2].$hex[2];

    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    $r = min(255, $r + (255 - $r) * $amount);
    $g = min(255, $g + (255 - $g) * $amount);
    $b = min(255, $b + (255 - $b) * $amount);

    return sprintf("#%02X%02X%02X", $r, $g, $b);
}

function timeDifference($time1, $time2)
{
    $time1 = new DateTime(date('Y-m-d') . ' ' . $time1);
    $time2 = new DateTime(date('Y-m-d') . ' ' . $time2);
    $interval = $time1->diff($time2);

    return [
        'hour' => $interval->h,
        'minute' => $interval->i,
    ];
}
