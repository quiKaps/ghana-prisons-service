<?php

namespace App\Http\Controllers;

use App\Models\Inmate;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;
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

            $template = view('pdf.inmate_profile', compact('record'))->render();

            // Generate PDF to a temporary location first
            $tempPath = storage_path('app/temp/inmate_profile.pdf');

            // Ensure temp directory exists
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }

            // Set margins (in mm)
            $top = 10;
            $right = 10;
            $bottom = 10;
            $left = 10;

            Browsershot::html($template)
                ->setNodeBinary(env('NODE_BINARY', '/usr/bin/node'))
                ->setNpmBinary(env('NPM_BINARY', '/usr/bin/npm'))
                ->margins($top, $right, $bottom, $left)
                ->save($tempPath);

            // Move to public storage using Laravel Storage
            Storage::disk('public')->put("{$record->full_name}.pdf", file_get_contents($tempPath));

            // Clean up temp file
            unlink($tempPath);

            // Get the public URL
            $url = Storage::url("{$record->full_name}.pdf");

            // Download the PDF and redirect back

            Notification::make()
                ->success()
                ->title('Profile Generation Successful')
                ->body('Profile PDF generated successfully. Open the PDF to view details and print')
                ->send();

            return response()->download(
                Storage::disk('public')->path("{$record->full_name}.pdf"),
                "{$record->full_name}.pdf"
            )->deleteFileAfterSend(true);
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
