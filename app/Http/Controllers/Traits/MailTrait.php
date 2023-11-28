<?php

namespace App\Http\Controllers\Traits;

use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;

trait MailTrait
{

    /**
     *
     */
    public static function sendMail($correos, $nombre_usuario, $asunto, $contenido, $archivos = [null], $correoscc = null, $correosbbc = null)
    {
        $correosEnviar = []; //De momento se mantiene
        $correosccEnviar = []; //De momento se mantiene
        $correosbbcEnviar = []; //De momento se mantiene

        if (!empty($correos)) {
            array_push($correosEnviar, $correos);
        }

        if (!empty($correoscc)) {
            array_push($correosccEnviar, $correoscc);
        }

        if (!empty($correosbbc)) {
            array_push($correosbbcEnviar, $correosbbc);
        }

        $datos_mail = new SendMail($asunto, $nombre_usuario, $contenido, $archivos);
        Mail::to($correosEnviar)
            ->cc($correosccEnviar)
            ->bcc($correosbbcEnviar)
            ->send($datos_mail);
    }
}
