<div class="container">

    <form method="post">

        <div class="form-row">
            <div class="form-group col-md">
                <label class="sr-only">از تاریخ</label>
                <div class="form-row">
                    <div class="form-group col-sm">
                        <label>روز شروع</label>
                        <select class="form-control" name="start_day">
                            <?php for ($i = 1; $i <= 31; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $start_day == $i ? 'selected="selected"' : '' ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group col-sm">
                        <label>ماه شروع</label>
                        <select class="form-control" name="start_month">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $start_month == $i ? 'selected="selected"' : '' ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group col-sm">
                        <label>سال شروع</label>
                        <input type="tel" pattern="[0-9]+" name="start_year" class="form-control"
                               value="<?php echo $start_year ?>"/>
                    </div>

                </div>
            </div>

            <div class="form-group col-md">
                <label class="sr-only">تا تاریخ</label>
                <div class="form-row">
                    <div class="form-group col-sm">
                        <label>روز پایان</label>
                        <select class="form-control" name="end_day">
                            <?php for ($i = 1; $i <= 31; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $end_day == $i ? 'selected="selected"' : '' ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group col-sm">
                        <label>ماه پایان</label>
                        <select class="form-control" name="end_month">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $end_month == $i ? 'selected="selected"' : '' ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group col-sm">
                        <label>سال پایان</label>
                        <input type="tel" pattern="[0-9]+" name="end_year" class="form-control"
                               value="<?php echo $end_year ?>"/>
                    </div>

                </div>
            </div>
        </div>

        <div>
            <input type="submit" class="btn btn-primary" value="دریافت گزارش"/>
        </div>
    </form>

</div>

<div class="container-fluid">
    <div class="table-responsive">
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th>عنوان</th>
                <th>ساعت کار انجام شده</th>
                <th>&</th>
            </tr>
            </thead>

            <tbody>
            <?php if ($query && $query instanceof PDOStatement): ?>
                <?php while ($row = $query->fetchObject()) { ?>
                    <tr>
                        <td><?php echo $row->title ?></td>
                        <td><?php echo convert_seconds($row->sum) ?></td>
                        <td>
                            <a href="<?php echo BASE_URL . "/reports/job_interval?job_id={$row->job_id}"; ?>">
                                گزارش دوره ای
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>