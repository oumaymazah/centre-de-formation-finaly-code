<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>Certificat de Réussite</title>
</head>

<body style="margin: 0; padding: 20px; font-family: Arial, sans-serif; background-color: #f5f7fa;">
  <div style="max-width: 900px; margin: auto; background: white; border: 1px solid #ccc; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); position: relative;">


    <div style="background: #2B6ED4; color: white; padding: 40px ; text-align: center; border-top-left-radius: 8px; border-top-right-radius: 8px;">
      <h1 style="margin: 0; font-size: 32px;">CERTIFICAT DE RÉUSSITE</h1>
      <p style="font-size: 14px;">Ce document certifie l'accomplissement avec succès du programme de formation</p>
    </div>


    <div style="padding: 40px 20px; text-align: center;">
      <p style="font-size: 18px;">Décerné à</p>
      <h2 style="font-size: 28px; color: #2B6ED4; margin: 20px 0;">{{ $user->name }} {{ $user->lastname}}</h2>
      <p style="font-size: 16px;">pour avoir complété avec succès la formation</p>
      <h3 style="font-size: 20px; font-weight: bold; margin: 20px 0;">{{ $training->title }}</h3>

      <div style="display: flex; justify-content: space-around; flex-wrap: wrap; margin-top: 30px;">
        <div style=" text-align: center;">
          <p style="font-size: 12px; color: #777;">Date d'obtention</p>
          <p style="font-size: 16px; font-weight: bold;">{{ $certification->obtained_date->format('d/m/Y') }}</p>
        </div>
        <div style=" text-align: center;">
          <p style="font-size: 12px; color: #777;">Niveau atteint</p>
          <p style="font-size: 16px; font-weight: bold;">Excellent</p>
        </div>
        <div style=" text-align: center;">
          <p style="font-size: 12px; color: #777;">Durée</p>
          <p style="font-size: 16px; font-weight: bold;">{{ $training->duration }} heures</p>
        </div>
      </div>

      <p style="margin-top: 40px; font-size: 15px; color: #555;">
        Ce certificat reconnaît les efforts et les compétences acquises durant cette formation.
      </p>
    </div>


<table style="width: 100%; margin-top: 40px; text-align: center; border-collapse: collapse;">
  <tr>

    <td>
      <div style="border-top: 1px solid #aaa; width: 150px; margin: auto;"></div>
      <p style="font-size: 13px;">Le Directeur Pédagogique</p>
      <p style="font-weight: bold;">Anis Saidi</p>
    </td>


    <td>
      <div style="border-top: 1px solid #aaa; width: 150px; margin: auto;"></div>
      <p style="font-size: 13px;">Le Formateur</p>
      <p style="font-weight: bold;">{{ $training->user->lastname }} {{ $training->user->name }}</p>
    </td>

   
    <td>
      <div style="border-top: 1px solid #aaa; width: 150px; margin: auto;"></div>
      <p style="font-size: 13px;">certificate_number</p>
      <p style="font-weight: bold;">N° {{ $certification->certificate_number }}</p>
    </td>
  </tr>
</table>


  </div>
</body>

</html>
