<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 1/6/2019
 * Time: 19:05
 */

namespace Utility\Assistant;


class ImageManager
{
    public function render_html()
    {
        ?>
        <div class="modal fade" id="UtilityAssistantImageManagerContainer" tabindex="-1" role="dialog"
             aria-labelledby="UtilityAssistantImageManagerContainerLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="UtilityAssistantImageManagerContainerLabel">انتخاب تصویر</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="image-container" class="card-columns">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <form enctype="multipart/form-data" onsubmit="return false" id="upload-image-form">
                            <input type="file" class="d-none" name="file" id="image-upload-input"
                                   onchange="uploadFile(this)" accept="image/*"/>

                            <div class="btn-group">
                                <input type="button" class="btn btn-warning" data-dismiss="modal" aria-label="Close"
                                       value="انصراف"/>

                                <button type="button" onclick="$('#image-upload-input').click()"
                                        class="btn btn-primary">
                                    آپلود تصویر جدید
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function render_script()
    {
        ?>
        <script>
            var IMG_PREVIEWER = '';
            var INPUT_URL_CONTAINER = '';

            function showImageSelectorModal(previewer_id, url_container_id) {
                retrieveImages();

                IMG_PREVIEWER = previewer_id;
                INPUT_URL_CONTAINER = url_container_id;

                $("#UtilityAssistantImageManagerContainer").modal('show');
            }

            function uploadFile(file) {
                var fd = new FormData();
                fd.append("file", file.files[0], file.files[0].name);

                ajax('POST', "<?php echo ADMIN_BASE_URL; ?>/tools/imageUpload", fd, false).then(function (r) {
                    if (r)
                        retrieveImages();
                })
            }

            function selectImage(image) {
                $("#" + IMG_PREVIEWER).attr('src', image);
                $("#" + INPUT_URL_CONTAINER).val(image);
                $("#UtilityAssistantImageManagerContainer").modal('hide');
            }

            function retrieveImages() {
                ajax("GET", "<?php echo ADMIN_BASE_URL; ?>/tools/imageManager").then(function (r) {
                    $('#image-container').empty();
                    r.forEach(function (itm) {
                        $("#image-container").append("<div  class='card cursor-pointer' onclick='selectImage(\"" + itm.url + "\")'>" +
                            "<img class=\"card-img-top\" src=\"" + itm.url + "\" alt=\"Card image cap\">" +
                            "</div>");
                    })
                })
            }
        </script>
        <?php
    }

    public function render_show_function($img_preview_id, $text_url_container_id)
    {
        echo "showImageSelectorModal('{$img_preview_id}','{$text_url_container_id}')";
    }
}