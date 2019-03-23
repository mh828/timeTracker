<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 12/3/2018
 * Time: 16:20
 */

namespace Utility\Assistant;


class Paging
{
    public $pagingQuery;

    /**
     * Paging constructor.
     * @param \PagingQuery $pagingQuery
     */
    public function __construct($pagingQuery)
    {
        $this->pagingQuery = $pagingQuery;
    }

    public function render_handler($start_from_one = false, $appended_data = array())
    {
        $first_page = $start_from_one ? 1 : 0;
        $last_page = $start_from_one ? $this->pagingQuery->pagesCount : $this->pagingQuery->pagesCount - 1;
        $next_page = $start_from_one ? $this->pagingQuery->page + 2 : $this->pagingQuery->page + 1;
        $previous_page = $start_from_one ? $this->pagingQuery->page : $this->pagingQuery->page - 1;


        $current = $start_from_one ? $this->pagingQuery->page + 1 : $this->pagingQuery->page;
        $form_data = '';
        foreach ($appended_data as $key => $data) {
            if ($key !== 'page')
                $form_data .= " <input type=\"hidden\" name=\"{$key}\" value=\"{$data}\" /> ";
        }

        if ($this->pagingQuery->pagesCount > 1) {
            ?>
            <div class="container">
                <div class="row">
                    <div class="col-4 d-flex justify-content-center align-items-center">
                        <?php if ($this->pagingQuery->page > 0): ?>
                            <form method="get" class="btn-group">
                                <?php echo $form_data; ?>

                                <button type="submit" name="page" class="btn btn-primary"
                                        value="<?php echo $first_page ?>"> صفحه اول
                                </button>
                                <button type="submit" name="page" class="btn btn-secondary"
                                        value="<?php echo $previous_page ?>">صفحه قبلی
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <div class="col-4 d-flex justify-content-center align-items-center">
                        <form method="get" class="d-flex justify-content-center align-items-center">
                            <?php echo $form_data; ?>

                            <input type="number" name="page" class="form-control mr-2"
                                   value="<?php echo $current; ?>"
                                   min="<?php echo $first_page ?>"
                                   max="<?php echo $last_page ?>">

                            <div class="text-nowrap"> از <b><?php echo $last_page ?></b></div>
                            <input type="submit" class="btn btn-primary" value="برو"/>
                        </form>
                    </div>

                    <div class="col-4 d-flex justify-content-center align-items-center">
                        <?php if ($this->pagingQuery->page < ($this->pagingQuery->pagesCount - 1)): ?>
                            <form method="get" class="btn-group ">
                                <?php echo $form_data; ?>

                                <button type="submit" name="page" class="btn btn-secondary"
                                        value="<?php echo $next_page ?>">
                                    صفحه بعدی
                                </button>
                                <button type="submit" name="page" class="btn btn-primary"
                                        value="<?php echo $last_page ?>">
                                    صفحه
                                    آخر
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    public function link_pagination($link_prefix, $show_count = 5)
    {
        if ($this->pagingQuery->pagesCount <= 1)
            return;

        $current = $this->pagingQuery->page + 1;

        $space = intval(($show_count / 2));
        $first = $current - $space;
        if ($first <= 0)
            $first = 1;
        $last = $space + $this->pagingQuery->pagesCount;
        if ($last > $this->pagingQuery->pagesCount)
            $last = $this->pagingQuery->pagesCount;

        $previous = $current - 1;
        if ($previous < 1)
            $previous = 1;
        $next = $current + 1;
        if ($next > $this->pagingQuery->pagesCount)
            $next = $this->pagingQuery->pagesCount;

        $for_max = $first + $show_count - 1;
        if ($for_max > $this->pagingQuery->pagesCount) {
            $for_max = $this->pagingQuery->pagesCount;
            if ($for_max - $first < $show_count && ($for_max - $show_count + 1) > 0)
                $first = $for_max - $show_count + 1;
        }
        ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($current == 1) ? "disabled" : '' ?>">
                    <a class="page-link" href="<?php echo $link_prefix . $previous ?>" tabindex="-1">قبلی</a>
                </li>

                <?php for ($i = $first; $i <= $for_max; $i++): ?>
                    <li class="page-item <?php echo ($i == $current) ? 'active' : '' ?>">
                        <a class="page-link" href="<?php echo $link_prefix . $i ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?php echo ($current >= $this->pagingQuery->pagesCount) ? "disabled" : '' ?>">
                    <a class="page-link" href="<?php echo $link_prefix . $next ?>">بعدی</a>
                </li>
            </ul>
        </nav>
        <?php
    }

    public function form_pagination($show_count = 5, $page_key = "page")
    {
        if ($this->pagingQuery->pagesCount <= 1)
            return;

        $current = $this->pagingQuery->page + 1;

        $space = intval(($show_count / 2));
        $first = $current - $space;
        if ($first <= 0)
            $first = 1;
        $last = $space + $this->pagingQuery->pagesCount;
        if ($last > $this->pagingQuery->pagesCount)
            $last = $this->pagingQuery->pagesCount;

        $previous = $current - 1;
        if ($previous < 1)
            $previous = 1;
        $next = $current + 1;
        if ($next > $this->pagingQuery->pagesCount)
            $next = $this->pagingQuery->pagesCount;

        $for_max = $first + $show_count - 1;
        if ($for_max > $this->pagingQuery->pagesCount) {
            $for_max = $this->pagingQuery->pagesCount;
            if ($for_max - $first < $show_count && ($for_max - $show_count + 1) > 0)
                $first = $for_max - $show_count + 1;
        }
        ?>
        <form method="get" aria-label="Page navigation">
            <?php foreach ($_GET as $k => $v) {
                if ($k == $page_key)
                    continue;
                ?>
                <input type="hidden" class="d-none" name="<?php echo $k ?>" value="<?php echo $v ?>"/>
            <?php } ?>

            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($current == 1) ? "disabled" : '' ?>">
                    <button type="submit" class="page-link"
                            name="<?php echo $page_key ?>"
                            value="<?php echo $previous ?>">
                        قبلی
                    </button>
                </li>

                <?php for ($i = $first; $i <= $for_max; $i++): ?>
                    <li class="page-item <?php echo ($i == $current) ? 'active' : '' ?>">
                        <button type="submit" class="page-link"
                                name="<?php echo $page_key ?>"
                                value="<?php echo $i ?>">
                            <?php echo $i ?>
                        </button>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?php echo ($current >= $this->pagingQuery->pagesCount) ? "disabled" : '' ?>">
                    <button type="submit" class="page-link"
                            name="<?php echo $page_key ?>"
                            value="<?php echo $next ?>">
                        بعدی
                    </button>
                </li>
            </ul>
        </form>
        <?php
    }

}