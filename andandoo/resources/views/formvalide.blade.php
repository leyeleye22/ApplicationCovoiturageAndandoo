<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation</title>
</head>
<style>
    @import url('https://fonts.googleapis.com/css?family=Lato&display=swap');

    body {
        user-select: none;
        font-family: 'Lato', sans-serif;
        background-color: white;
        display: flex;
        align-items: center;
        flex-direction: column;
        justify-content: center;
        width: 100%;
        height: 100vh;
        padding: 12px;
    }

    .header {
        color: orange;
        font-size: 2rem;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .input {
        display: inline-block;
        float: left;
        background: #b2adad7d;
        border: none;
        position: relative;
        border-radius: 0.25rem;
        width: 1.5rem;
        height: auto;
        color: black;
        font-size: 2.5rem;
        margin-right: .5rem;
        padding: .5rem .75rem;
    }

    .input:last-child {
        margin: 0;
    }

    .center {
        display: flex;
        align-items: center;
        flex-direction: column;
        width: 90%;
        padding: 8px;
    }

    .keys {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        grid-template-rows: 1fr;
    }

    .buttonEnvoyer {
        margin-top: 25px;
        height: 40px;
        width: 130px;
        border-radius: 8px;
        border: none;
        background-color: orange;
        color: white;
        font-size: 1.2em;
    }

    .buttonContainer {
        display: flex;
        justify-content: center;
    }
</style>

<body>

    <div class="center">
        <p class="header">Entrer votre code de validation</p>
        <form action="http://127.0.0.1:8000/api/validation.code" method="POST">
            @csrf
            <div class="keys">
                <input type="text" class="input" name="val1" maxlength="1">
                <input type="text" class="input" name="val2" maxlength="1">
                <input type="text" class="input" name="val3" maxlength="1">
                <input type="text" class="input" name="val4" maxlength="1">
                <input type="text" class="input" name="val5" maxlength="1">
                <input type="text" class="input" name="val6" maxlength="1">
            </div>
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="buttonContainer">
                <input type="submit" class="buttonEnvoyer"> </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var inputs = document.querySelectorAll('.input');

            inputs.forEach(function(input, index, arr) {
                input.addEventListener('input', function() {
                    // Allow only single-digit numbers
                    this.value = this.value.replace(/[^0-9]/g, '');

                    if (this.value.length === 1) {
                        // Move focus to the next input field
                        var nextInput = arr[index + 1];
                        if (nextInput) {
                            nextInput.focus();
                        }
                    }
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value.length === 0) {
                        // Move focus to the previous input field
                        var prevInput = arr[index - 1];
                        if (prevInput) {
                            prevInput.focus();
                        }
                    }
                });
            });
        });
    </script>
    <SCript>
        //lets
        let input = document.querySelectorAll(".input");
        let reloadBtn = document.querySelector(".reload");
        let color = document.querySelector(".stateColor");
        let pinSet = false;
    </SCript>
</body>

</html>
