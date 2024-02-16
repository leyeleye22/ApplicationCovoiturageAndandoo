<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Réservation</title>
</head>

<body style="background-color: #E9E9E9; font-family: Arial, sans-serif;">

    <div
        style="background-color: #E9E9E9; margin: 0 auto; width: 60%; height: 100%; align-items: center; display: flex; flex-direction: column; justify-content: center; border-radius: 12px; padding: 0.9em;">
        <h1 style="color: rgb(28, 141, 28); text-align: center;">Confirmation de Réservation - Trajet Ouakam vers Yoff
        </h1>

        <p style="text-align: center;">Cher(e) {{ $data[0]['NomClients'] }},</p>

        <p style="text-align: center;">Nous sommes ravis de vous iNomClientsnformer que votre réservation de trajet a été
            confirmée avec succès! Ci-dessous, vous trouverez les détails de votre voyage:</p>
        <div
            style="height: 90%; width: 50%; background-color: white; border-radius: 10px; display: grid; grid-template-rows: 20% 40% 40%;">
            <div
                style="padding: 1.6em; border-bottom: thin solid #FA7436; display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: 1fr; gap: 5%;">
                <div style="display: flex; align-items: center;">
                    <img src="https://img.freepik.com/vecteurs-libre/vecteur-degrade-logo-colore-oiseau_343694-1365.jpg?w=826&t=st=1707233446~exp=1707234046~hmac=72cc1a5810f283522f64925d8170360da6fa21abfc174c9172774b5bf5e80a23"
                        alt="" style="height: 50px; width: 50px;">
                </div>
                <div style="display: flex; align-items: center;">
                    <h3 style="width: 100%; text-align: end; font-size: 0.9em;"><span
                            style="font-weight: lighter;">Ticket N°: &nbsp;</span>A00340916DF</h3>
                </div>
            </div>
            <div
                style="border-bottom: thin solid #FA7436; display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: 1fr; gap: 5%; padding: 1.6em;">
                <div
                    style="background-color: #fa7436; backdrop-filter: blur(15px); border-radius: 6px; padding: 1em; color: white;">
                    <h3 style="margin: 0; padding: 0;">Lieu de départ</h3>
                    <p style="margin: 0; padding: 0;">{{ $data[0]['LieuDepart'] }}</p>
                </div>
                <div
                    style="background-color: #fa7436; backdrop-filter: blur(15px); border-radius: 6px; padding: 1em; color: white;">
                    <h3 style="margin: 0; padding: 0;">Lieu d'arrivée</h3>
                    <p style="margin: 0; padding: 0;">{{ $data[0]['LieuArrivee'] }}</p>
                </div>
            </div>
            <div
                style="display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: 1fr; gap: 3%; padding: 1.6em;">
                <div>
                    <label for="" style="margin: 0; padding: 0;">Date de réservation</label>
                    <h4 for="" style="margin: 0; padding: 0;">{{ $data[0]['DateDepart'] }}</h4>
                    <label for="" style="margin: 0; padding: 0;">Heure de départ</label>
                    <h4 style="margin: 0; padding: 0;">{{ $data[0]['HeureD'] }}</h4>
                </div>
                <div>
                    <label for="" style="margin: 0; padding: 0;">Nom complet du chaufeur</label>
                    <h4 for="" style="margin: 0; padding: 0;">{{ $data[0]['NomChauffeur'] }}</h4>
                    <label for="" style="margin: 0; padding: 0;">Numéro de Téléphone</label>
                    <h4 style="margin: 0; padding: 0;">{{ $data[0]['TelephoneChauffeur'] }}</h4>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
