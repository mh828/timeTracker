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
                <div class="d-flex align-items-center justify-content-center">
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

                    <form method="get" class="form-inline  align-items-center mr-1 ml-2">
                        <?php echo $form_data; ?>

                        <input type="number" name="page" class="form-control mr-2"
                               value="<?php echo $current; ?>"
                               min="<?php echo $first_page ?>"
                               max="<?php echo $last_page ?>">

                        <div> از <b><?php echo $last_page ?></b></div>
                        <input type="submit" class="btn btn-primary" value="برو"/>
                    </form>

                    <?php if ($this->pagingQuery->page < ($this->pagingQuery->pagesCount - 1)): ?>
                        <form method="get" class="btn-group">
                            <?php echo $form_data; ?>

                            <button type="submit" name="page" class="btn btn-secondary"
                                    value="<?php echo $next_page ?>">
                                صفحه بعدی
                            </button>
                            <button type="submit" name="page" class="btn btn-primary" value="<?php echo $last_page ?>">
                                صفحه
                                آخر
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
    }
}