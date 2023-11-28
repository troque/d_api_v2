<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    protected $usuario;
    protected $asunto;
    protected $adjuntos;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $usuario, $asunto, $adjuntos)
    {
        $this->usuario = $usuario;
        $this->asunto = $asunto;
        $this->adjuntos = $adjuntos;
        $this->subject($subject);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->view(
            'mail.mail',
            [
                'usuario' => $this->usuario,
                'asunto' => $this->asunto
            ]
        );

        if($this->adjuntos){
            foreach ($this->adjuntos as $adjunto) {
                if($adjunto != null){
                    $mail->attach($adjunto);
                }
            }
        }

        return $mail;
    }
}
