<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lien</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <style>
        .containerMail {
            width: 60%;
            padding: 20px;
            margin: 0 auto;
            box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px;
        }

        .logo {
            display: flex;
            justify-content: end;
        }

        .logo img {
            width: 20%;
            height: 20%;
        }

        .text h4,
        h2 {
            text-align: center;
        }

        .text p,
        h6 {
            margin-left: 8rem;
        }
    </style>
</head>

<body>
    <div class="containerMail">
        <div class="logo">
            <img src="https://i.ibb.co/N783Ftt/logo-andandoo.jpg" alt="logo-andandoo">
        </div>
        <div class="text">
            <h4>Voici votre lien de Réinialisation</h4>
            <hr>
            <p>utiliser ce lien pour réinialiser</p>
            <h4><a href="{{ route('reset.password.get', $token) }}">lien de rénialisation </a></h4>
            <h6>Ce code est valide pendant 1 heure.</h6>
        </div>
    </div>
</body>

</html>
