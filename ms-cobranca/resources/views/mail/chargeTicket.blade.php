<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket of debts</title>
</head>
<body>
    <p>Hi {{ $name }}</p>
    <p>This ticket this debt {{ $ticket['debtId'] }} was created.</p>
    This ticket of $ {{ $ticket['debtAmount'] }} whth Due Date on {{ $ticket['debtDueDate'] }}.

    BarCode payment:  {{ $ticket['barCode'] ?? 0 }}
</body>
</html>
