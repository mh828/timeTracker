<?php
function body()
{
    $job_id = $_GET['id'] ?? null;
    if (!$job_id) {
        header('location: /jobs');
        return;
    }
    $pdo = get_pdo();
    $query = $pdo->prepare('SELECT * FROM job WHERE job_id = :job_id');
    $query->bindValue(':job_id', $job_id);
    $query->execute();
    $job = $query->fetchObject();
    if (!$job) {
        header('location: /jobs');
        return;
    }
    $errors = [];
    if (strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
        $title = trim($_POST['title'] ?? '');
        if (empty($title))
            $errors['title'] = 'عنوان تعیین نشده است';

        if (count($errors) === 0) {
            $query = $pdo->prepare("Update job SET 'title' = :title WHERE job_id = :job_id");
            $query->bindValue(':title', $title);
            $query->bindValue(':job_id', $job_id);
            $query->execute();
            header('location: /jobs');
        }
    }
    ?>

    <form method="post" class="container">
        <div class="form-group">
            <label>عنوان</label>
            <input type="text" name="title" value="<?= $job->title ?>" class="form-control"/>
        </div>

        <ul class="mt-2 text-danger">
            <?php foreach ($errors as $error) : ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>

        <div class="mt-2 text-center">
            <button type="submit" name="save-changes" class="btn btn-success">
                ثبت تغییرات
            </button>
        </div>
    </form>

    <?php
}
