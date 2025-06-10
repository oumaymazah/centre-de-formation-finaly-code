<!DOCTYPE html>
<html>
<head>
    <title>Confirmation de réservation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .lastname{
            font-weight: bold;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 0;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            padding: 20px;
            background-color: #2B3B4E;
            color: white;
            width: 100%;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .content {
            padding: 25px;
            max-width: 600px;
            margin: 0 auto;
        }
        .footer {
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            width: 100%;
            background-color: #f7f9fc;
        }
        .contact-info {
            margin: 15px 0;
            line-height: 1.8;
        }
        .code {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin: 30px 0;
            letter-spacing: 1px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2B6ED4;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
            text-align: center;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            padding: 15px;
            background-color: #2B6ED4;
            color: white;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .subject-line {
            background-color: #e9ecef;
            padding: 10px 25px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
        </div>
        
        <div class="content">
            <h2 style="color: #333; margin-top: 0; margin-bottom: 20px; font-size: 24px;">Confirmation de votre réservation</h2>

            <p>Bonjour <span class="lastname"><?php echo e($reservation->user->lastname ?? 'Étudiant'); ?></span>,</p>
            
            <p>Nous confirmons avoir reçu votre paiement de <strong><?php echo e(number_format($totalPrice, 2, ',', ' ')); ?> Dt</strong> effectué le <strong> <?php echo e(\Carbon\Carbon::parse($reservation->payment_date)->format('d/m/Y à H:i')); ?></strong>.
                 Votre réservation est désormais validée. Merci pour votre confiance !</p>
            
            <p>Numéro de réservation: <strong><?php echo e($reservation->id); ?></strong></p>
        </div>
        
        <div class="footer">
            <div class="contact-info">
                
                
                
            </div>
            <p>Ce message est automatique, merci de ne pas y répondre.</p>
            <p>© <?php echo e(date('Y')); ?> EMPOWERMENT LEARNING SUCCESS . Tous droits réservés.</p>
             <p>Rue farabi trocadéro, immeuble kraiem 1 étage</p>
                <i class="fas fa-phone-alt me-1"></i> 52450193 / 21272129<br>
        </div>
    </div>
</body>
</html><?php /**PATH C:\Users\hibah\Downloads\P_Plateforme_ELS\P_Plateforme_ELS\resources\views/emails/reservation-confirmation.blade.php ENDPATH**/ ?>