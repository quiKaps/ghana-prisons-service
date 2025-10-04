<?php

namespace App\Http\Controllers;

use App\Models\Inmate;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;

class PrintController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Inmate $inmate)
    {
        $record = $inmate;
        try {

            $pdf = Pdf::loadView('pdf.inmate_profile', compact('record'));

            // For download instead of stream (more reliable in Filament actions)
            // Set PDF to A4 and center content
            $pdf->setPaper('a4', 'portrait');

            // Optionally, you can pass a variable to the view to help center content via CSS
            $profileData['centerContent'] = true;

            // return response()->streamDownload(
            //     fn() => print($pdf->output()),
            //     "prisoner_profile_{$inmate->full_name}.pdf",
            //     ['Content-Type' => 'application/pdf']
            // );

            // open in browser
            return $pdf->stream("inmate_profile_{$record->id}.pdf");
        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title('Print Failed')
                ->body('An error occurred while generating the PDF: ' . $e->getMessage())
                ->send();

            // Return null or throw exception to prevent further processing
            throw $e;
        }
    }
}
