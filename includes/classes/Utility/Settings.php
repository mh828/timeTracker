<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 1/5/2019
 * Time: 16:21
 */

namespace Utility;

include_once 'includes/settings.php';
include_once 'includes/functions.php';

class Settings
{
    private $current_settings;

    const WEBSITE_GENERAL_TITLE = 'WEBSITE_GENERAL_TITLE';
    const WEBSITE_GENERAL_DESCRIPTION = 'WEBSITE_GENERAL_DESCRIPTION';
    const WEBSITE_GENERAL_KEYWORDS = 'WEBSITE_GENERAL_KEYWORDS';
    const CONTENT_DEFAULT_IMAGE = 'CONTENT_DEFAULT_IMAGE';
    const WORKSHOP_DEFAULT_IMAGE = 'WORKSHOP_DEFAULT_IMAGE';

    const WEBSITE_IMAGE_FILENAME = ROOT_DIR . '/resources/images/WEBSITE_MAIN_IMAGE';


    public function __construct()
    {
        $this->refresh();
    }

    public function refresh()
    {
        $this->current_settings = web_settings();
    }

    /**
     * @param $key
     * @return mixed|bool
     */
    public function get_by_key($key)
    {
        if (is_array($this->current_settings) && isset($this->current_settings[$key]))
            $this->current_settings[$key];
        else if (is_object($this->current_settings) && isset($this->current_settings->$key))
            return $this->current_settings->$key;
        else
            return false;
    }

    /**
     * @param $data
     * @return $this
     */
    public function update_settings($data)
    {
        $data = input_validate($data);
        $this->current_settings = array_merge((array)$this->current_settings, (array)$data);
        return $this;
    }

    /**
     * @return bool|int
     */
    public function save_settings()
    {
        if ($res = web_settings_save($this->current_settings))
            $this->refresh();

        return $res;
    }

    public static function save_image($file)
    {
        if (!empty($file['tmp_name']) && !empty($file['name']) && is_image($file['tmp_name'])) {
            $old_files = glob(self::WEBSITE_IMAGE_FILENAME . ".*");
            foreach ($old_files as $old_file) {
                unlink($old_file);
            }
            move_uploaded_file($file['tmp_name'],
                self::WEBSITE_IMAGE_FILENAME . substr($file['name'], strrpos($file['name'], '.')));
        }
    }

    public static function get_image()
    {
        $files = glob(self::WEBSITE_IMAGE_FILENAME . ".*");
        if (count($files) > 0)
            return str_replace(ROOT_DIR, BASE_URL, $files[0]);
    }
}