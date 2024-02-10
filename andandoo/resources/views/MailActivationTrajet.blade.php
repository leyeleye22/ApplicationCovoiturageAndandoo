<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Réservation</title>
    <style>
        h1 {
            color: rgb(28, 141, 28);
            text-align: center;
        }

        p{
            text-align: center;
        }

    

        .mainTicket {
            background-color: #E9E9E9;
            margin: 0 auto;
            width: 60%;
            height: 100%;
            align-items: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 12px;
            padding: 0.9em;


        }

        .ticket {
            height: 90%;
            width: 50%;
            background-color: white;
            border-radius: 10px;
            display: grid;
            grid-template-rows: 20% 40% 40%;
        }

        .ticketHeader {
            padding: 1.6em;
            border-bottom: thin solid #FA7436;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr;
            gap: 5%;
        }

        .ticketHeaderLogo {
            display: flex;
            align-items: center;

            img {
                height: 50px;
                width: 50px;
            }
        }

        .ticketHeaderCode {
            display: flex;
            align-items: center;

            h3 {
                width: 100%;
                text-align: end;
                font-size: 0.9em;

                span {
                    font-weight: lighter;
                }
            }
        }

        .ticketBody {
            border-bottom: thin solid #FA7436;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr;
            gap: 5%;
            padding: 1.6em;

            .ticketBodyItem {
                background-color: #fa7436;
                backdrop-filter: blur(15px);
                border-radius: 6px;
                padding: 1em;
                color: white;
            }
        }

        .ticketFooter {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr;
            gap: 3%;
            padding: 1.6em;
        }

        @media screen And (width<= 830px) {
            .mainTicket {
            background-color: #E9E9E9;
            margin: 0 auto;
            width: 80%;
            height: 100%;
            align-items: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 12px;
            padding: 0.9em;


        }

        .ticket {
            height: 90%;
            width: 100%;
            background-color: white;
            border-radius: 10px;
            display: grid;
            grid-template-rows: 20% 40% 40%;
        }
        }
    </style>
</head>

<body>
    <div class="mainTicket">
        <h1>Confirmation de Réservation - Trajet Ouakam vers Yoff</h1>

        <p>Cher(e) [Nom du Client],</p>

        <p>Nous sommes ravis de vous informer que votre réservation de trajet a été confirmée avec succès! Ci-dessous, vous
            trouverez les détails de votre voyage:</p>
        <div class="ticket">
            <div class="ticketHeader">
                <div class="ticketHeaderLogo">
                    <img src="https://img.freepik.com/vecteurs-libre/vecteur-degrade-logo-colore-oiseau_343694-1365.jpg?w=826&t=st=1707233446~exp=1707234046~hmac=72cc1a5810f283522f64925d8170360da6fa21abfc174c9172774b5bf5e80a23"
                        alt="">
                </div>
                <div class="ticketHeaderCode">
                    <h3><span>Ticket N°: &nbsp;</span>A00340916DF</h3>
                </div>
            </div>
            <div class="ticketBody">
                <div class="ticketBodyItem">
                    <h3>Lieu de départ</h3>
                    <p>Ouakam</p>
                </div>
                <div class="ticketBodyItem">
                    <h3>Lieu d'arrivée</h3>
                    <p>Yoff</p>
                </div>
            </div>
            <div class="ticketFooter">
                <div class="infoVoyage">
                    <label for="">Date de réservation</label>
                    <h4 for="">06/02/2024</h4>
                    <label for="">Heure de départ</label>
                    <h4>14:30mn</h4>
                </div>
                <div class="InfoChaufeur">
                    <label for="">Nom complet du chaufeur</label>
                    <h4 for="">Moussa Sy</h4>
                    <label for="">Numéro de Téléphone</label>
                    <h4>77 340 98 76</h4>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
