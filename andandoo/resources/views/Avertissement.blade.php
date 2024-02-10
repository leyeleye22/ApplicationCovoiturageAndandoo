<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Avertissement</title>
    <style>
        .card {
            overflow: hidden;
            position: relative;
            background-color: #ffffff;
            text-align: left;
            border-radius: 0.5rem;
            max-width: 490px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            margin: 0 auto;
        }

        .header {
            padding: 1.25rem 1rem 1rem 1rem;
            background-color: #ffffff;
        }

        .image {
            display: flex;
            margin-left: auto;
            margin-right: auto;
            background-color: #FEE2E2;
            flex-shrink: 0;
            justify-content: center;
            align-items: center;
            width: 3rem;
            height: 3rem;
            border-radius: 9999px;
        }

        .image i {
            color: #DC2626;
            width: 1.5rem;
            height: 1.5rem;
            text-align: center;
        }

        .content {
            margin-top: 0.75rem;
            text-align: center;
        }

        .title {
            color: #111827;
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.5rem;
        }

        .message {
            margin-top: 0.5rem;
            color: #6B7280;
            font-size: 0.875rem;
            line-height: 1.25rem;
            text-align: justify;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="header">
            <div class="image">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="content">
                <span class="title">Avertissement</span>
                <p class="message">{{ $avertissements }}</p>
            </div>
        </div>
    </div>
</body>

</html>
