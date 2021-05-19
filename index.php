<?php
const ERROR_REQUIRED = 'Veuillez renseigner une todo';
const ERROR_TOO_SHORT = 'Veuillez entrer au moins 5 caractères';
$error = '';
$todo = '';
$filename = __DIR__ . "/data/todos.json";
$todos = [];

if (file_exists($filename)) {
    $data = file_get_contents($filename);
    $todos = json_decode($data, true) ?? [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST = filter_input_array(INPUT_POST, [
        "todo" => [
            "filter" => FILTER_SANITIZE_STRING,
            "flags" => FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_BACKTICK
        ]
    ]);
    $todo = $_POST['todo'] ?? '';

    if (!$todo) {
        $error = ERROR_REQUIRED;
    } else if (mb_strlen($todo) < 5) {
        $error = ERROR_TOO_SHORT;
    }

    if (!$error) {
        array_push($todos, [
            'name' => $todo,
            'done' => false,
            'id' => time()
        ]);
        file_put_contents($filename, json_encode($todos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        header('Location: /');
    }
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require_once 'includes/head.php'; ?>
    <title>TODO</title>
</head>


<body>
<div class="container">
    <?php require_once 'includes/header.php' ?>
    <div class="content">
        <div class="todo-container">
            <h1>Ma Todo</h1>
            <form class="todo-form" action="/" method="post">
                <input value="<?= $todo; ?>" name="todo" type="text">
                <button class="btn btn-primary"><i class="fas fa-plus-square"></i>Ajouter</button>
            </form>
            <?php if ($error) : ?>
                <p class="text-danger"><?= $error ?></p>
            <?php endif; ?>
            <ul class="todo-list">
                <?php foreach ($todos as $todo) : ?>
                    <li class="todo-item <?= $todo['done'] ? 'low-opacity' : ''; ?>">
                        <span class="todo-name"><?= $todo['name'] ?></span>
                        <a href="/edit-todo.php?id=<?= $todo['id']; ?>">
                            <button class="btn btn-primary btn-small">
                                <?= $todo['done'] ? '<i class="fas fa-window-close"></i>Annuler' : '<i class="fas fa-check"></i>Valider' ?>
                            </button>
                        </a>
                        <a href="/remove-todo.php?id=<?= $todo['id']; ?>">
                            <button class="btn btn-danger btn-small"><i class="fas fa-trash-alt"></i>Delete</button>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

    </div>
    <?php require_once 'includes/footer.php' ?>
</div>
</body>

</html>