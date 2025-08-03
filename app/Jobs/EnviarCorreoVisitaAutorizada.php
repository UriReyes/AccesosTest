<?php
namespace App\Jobs;

use App\Mail\VisitaAutorizada;
use App\Models\Visita;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EnviarCorreoVisitaAutorizada implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $visita;

    public function __construct(Visita $visita)
    {
        $this->visita = $visita;
    }

    public function handle(): void
    {
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $this->visita->qr_token,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            // Quitar logoPath, labelText, etc. si no quieres
        );

        $result = $builder->build();

        $filename = 'qr_visita_' . Str::uuid() . '.png';
        $tempPath = storage_path("app/temp/{$filename}");

        file_put_contents($tempPath, $result->getString());

        Mail::to($this->visita->user->email)
            ->send(new VisitaAutorizada($this->visita, $tempPath));

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
    }
}
