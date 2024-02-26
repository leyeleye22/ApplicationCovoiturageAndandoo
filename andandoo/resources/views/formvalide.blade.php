<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de Validation</title>
    <style>
        .form {
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: space-around;
            background-color: white;
            width: 380px;
            border-radius: 12px;
            padding: 20px;
            margin: 9px auto;
            box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            color: black
        }

        .inputs {
            margin-top: 10px
        }

        .inputs input {
            width: 32px;
            height: 32px;
            text-align: center;
            border: none;
            border-bottom: 1.5px solid #d2d2d2;
            margin: 0 10px;
        }

        .inputs input:focus {
            border-bottom: 1.5px solid #FA7436;
            outline: none;
        }

        .action {
            margin-top: 24px;
            padding: 12px 16px;
            border-radius: 8px;
            border: none;
            background-color: #FA7436;
            color: white;
            cursor: pointer;
            align-self: end;
        }
    </style>
</head>

<body>
    <form class="form">
        <div class="title">Code de Verification</div>
        <div class="inputs">
            <input id="input1" type="text" maxlength="1">
            <input id="input2" type="text" maxlength="1">
            <input id="input3" type="text" maxlength="1">
            <input id="input4" type="text" maxlength="1">
            <input id="input5" type="text" maxlength="1">
            <input id="input5" type="text" maxlength="1">
        </div>
        <input type="hidden" name="token" value="{{ $token }}"><button class="action">verifier</button>
    </form>
</body>

</html>
