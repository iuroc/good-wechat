<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>机器人控制台</title>
    <link rel="stylesheet" href="https://cdn.staticfile.org/bootstrap/5.2.3/css/bootstrap.min.css">
    <script src="./poncon.js"></script>
</head>

<body>
    <div class="container py-4">
        <h4 class="mb-3">机器人控制台</h4>
        <div class="mb-3">
            <button class="btn btn-success tab-home">关键词回复</button>
        </div>
        <div class="poncon-home poncon-page" style="display: none;">
            
        </div>
    </div>
    <script>
        const poncon = new Poncon()
        poncon.setPageList(['home'])
        poncon.start()
    </script>
</body>

</html>