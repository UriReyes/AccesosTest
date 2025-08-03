<?php
namespace App\Jobs;

use App\Mail\VisitaRechazada;
use App\Models\Visita;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EnviarCorreoVisitaRechazada implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $visita;

    public function __construct(Visita $visita)
    {
        $this->visita = $visita;
    }

    public function handle(): void
    {
        Mail::to($this->visita->user->email)
            ->send(new VisitaRechazada($this->visita));
    }
}
