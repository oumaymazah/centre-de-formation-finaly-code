<?php

namespace App\Jobs;

use App\Mail\ReservationConfirmationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReservationConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reservation;
    protected $totalPrice;
    protected $email;

    /**
     * Le nombre de fois que le job peut être tenté.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Le nombre de secondes avant que le job soit à nouveau disponible.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * Create a new job instance.
     *
     * @param mixed $reservation
     * @param float $totalPrice
     * @param string $email
     * @return void
     */
    public function __construct($reservation, $totalPrice, $email = null)
    {
        $this->reservation = $reservation;
        $this->totalPrice = $totalPrice;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Utiliser l'email passé en paramètre ou celui de l'utilisateur associé à la réservation
            $emailTo = $this->email ?? $this->reservation->user->email;
            
            Log::info("SendReservationEmailJob: Tentative d'envoi d'email à: " . $emailTo);
            
            Mail::to($emailTo)->send(new ReservationConfirmationMail($this->reservation, $this->totalPrice));
            
            Log::info("SendReservationEmailJob: Email envoyé avec succès");
        } catch (\Exception $e) {
            Log::error("SendReservationEmailJob: Erreur lors de l'envoi de l'email: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            throw $e; // Relancer l'exception pour que le job soit considéré comme échoué
        }
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error("SendReservationEmailJob: Job échoué définitivement - " . $exception->getMessage());
        
        // Vous pourriez implémenter une notification alternative ici
        // Par exemple, notifier un administrateur via un autre canal
    }
}