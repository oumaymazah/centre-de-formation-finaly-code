<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rappel de formation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4a6cf7;
            color: #fff;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .important {
            font-weight: bold;
            color: #4a6cf7;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Rappel de formation</h1>
        </div>
        <div class="content">
            <p>Bonjour <?php echo e($user->name); ?> <?php echo e($user->lastname); ?>,</p>
            
            <p>Nous vous rappelons que votre formation <span class="important"><?php echo e($training->title); ?></span> débutera dans <span class="important">2 jours</span>, le <span class="important"><?php echo e($startDate); ?></span>.</p>
            
            <p>Voici un résumé de votre formation :</p>
            <ul>
                <li><strong>Titre :</strong> <?php echo e($training->title); ?></li>
                <li><strong>Date de début :</strong> <?php echo e($startDate); ?></li>
                <li><strong>Formateur :</strong> <?php echo e($training->user->name ?? 'Non spécifié'); ?> <?php echo e($formation->user->lastname ?? ''); ?></li>
            </ul>
            
            <p>Pour toute question ou information supplémentaire, n'hésitez pas à nous contacter.</p>
            
            <p>Cordialement,</p>
        </div>
        <div class="footer">
            <p>Ce message est un rappel automatique. Merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html><?php /**PATH C:\Users\hibah\Downloads\P_Plateforme_ELS\P_Plateforme_ELS\resources\views/emails/formation-reminder.blade.php ENDPATH**/ ?>