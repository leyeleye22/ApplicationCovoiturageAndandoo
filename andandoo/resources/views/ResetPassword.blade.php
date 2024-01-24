</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>reset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .containerMail {
            width: 60%;
            padding: 25px;
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

        .text h4 {
            text-align: center;
        }

        button {
            background-color: #FA7436 !important;
            color: white !important;
            border: none;
            float: right;
            padding: 8px;
        }
    </style>
</head>

<body>
    <div class="containerMail">
        <div class="logo">
            <img src="https://i.ibb.co/N783Ftt/logo-andandoo.jpg" alt="LOGO">
        </div>
        <div class="text">
            <h4>Reinitialisation mot de passe</h4>
            <hr>
            <form action="{{ route('reset.password.post') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Nouveau Mot de Passe:</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" name="password">
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Confirmation du Mot de Passe:</label>
                    <input type="password" class="form-control" id="exampleInputPassword1"name="password_confirmation">
                </div>
                <button type="submit" class="btn positionbtn">Enregistrer</button>
            </form>
        </div>
    </div>
</body>

</html>
