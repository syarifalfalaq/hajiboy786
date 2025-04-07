<?php

namespace App\Http\Controllers;

use App\Models\Resiumum;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ResiumumPrintController extends Controller
{
    public function print(Resiumum $resiumum)
    {
        return view('resiumum.print', compact('resiumum'));
    }

    public function pdf(Resiumum $resiumum)
    {
        $pdf = Pdf::loadView('resiumum.print', compact('resiumum'));
        return $pdf->download('resiumum-' . $resiumum->noresi . '.pdf');
    }
}
