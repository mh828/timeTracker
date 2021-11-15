<?php
function body()
{
    $pdo = get_pdo();
    $page = $_GET['page'] ?? 1;
    $per_page = $_GET['per_page'] ?? 20;
    $query = $pdo->query('Select COUNT(*) as count FROM job');
    $total_rows = $query->fetchColumn(0);
    $pages_count = ceil($total_rows / $per_page);

    $query = $pdo->prepare("SELECT * FROM job ORDER BY 'title' LIMIT :limit OFFSET :offset");
    $query->bindValue(':limit', $per_page);
    $query->bindValue(':offset', $per_page * ($page - 1));
    $query->execute()

    ?>
    <h1 class="mt-4">مدیریت شغل ها (فعالیت ها)</h1>

    <div class="container">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>ایدی</th>
                    <th>عنوان</th>
                    <th>&</th>
                </tr>
                </thead>

                <tbody>
                <?php while ($p = $query->fetch(PDO::FETCH_OBJ)) : ?>
                    <tr>
                        <td><?= $p->job_id ?></td>
                        <td><?= $p->title ?></td>
                        <td>

                            <a href="" class="btn btn-primary">
                                ویرایش
                            </a>

                            <a href="" class="btn btn-danger ml-1">
                                حذف
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <form class="container mt-3 d-flex align-items-center" style="max-width: 350px">
        <input type="hidden" name="per_page" value="<?= $per_page ?>"/>
        <select name="page" class="form-control">
            <?php for ($i = 0; $i < $pages_count; $i++) : ?>
                <option <?= $page === ($i + 1) ? 'selected' : '' ?>><?= $i + 1 ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit" class="btn btn-primary">
            برو
        </button>
    </form>
    <?php
}
