<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=STATIC_FILES?>assets/css/main.css">
    <title>Mvc todo app</title>
</head>
<body>
    <div class="main-container">
        <h1>TodoApp</h1>
        <button class="add">Add Todo</button>
        <div class="content">
            <h3>Todos</h3> <hr>
            <ul class="todos">
                <?php foreach($todos as $todo){?>
                    <li class="todo">
                        <p><?=$todo?></p>
                        <span class="controls">
                            Manage
                        </span>
                    </li>
                <?php }?>
                
            </ul>
        </div>
    </div>
    <script src="<?=STATIC_FILES?>assets/js/main.js"></script>
</body>
</html>