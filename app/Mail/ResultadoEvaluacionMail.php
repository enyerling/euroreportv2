<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResultadoEvaluacionMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $pdfPath;

    /**
     * Create a new message instance.
     *
     * @param string $pdfPath
     * @return void
     */
    public function __construct($pdfPath)
    {
        $this->pdfPath = $pdfPath;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.resultado_evaluacion')
                    ->attachData($this->pdfPath, 'Resultado_Evaluacion.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}