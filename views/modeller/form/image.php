<?php

$uniqid = uniqid();
        echo '<script type="text/javascript">
                    $(function(){
                        $(\'#'.$this->_column.'_imgtf\').change(function(){
                                $(\'#'.$this->_column.'_imglink\').attr(\'href\',\''.BASE_URL.'public/data/uploads/images/\'+$(this).val()+\'\');
                        });

                        var uploader = new qq.FileUploader({
                            element: document.getElementById(\'imageUploadButton'.$uniqid.'\'),
                            action: "'.BASE_URL.'admin/image",
                            multiple: false,
                            allowedExtensions: ["jpg","png","jpeg"],
                            disableDefaultDropzone: true,
                            template: \'<div class="qq-uploader"><div class="qq-upload-drop-area"></div><div class="btn qq-upload-button btn_uploadphoto">Upload Image</div><div class="qq-upload-list"></div></div>\',
                            onComplete: function(id,fileName,responseJSON){
                                $(\'#uploadedImage\').attr(\'src\', "'.BASE_URL.'public/data/uploads/images/"+responseJSON.filename);
                                $("#'.$this->_column.'_imgtf").val( responseJSON.filename );
                                $(\'#'.$this->_column.'_imglink\').attr(\'href\',\''.BASE_URL.'public/data/uploads/images/\'+responseJSON.filename+\'\');
                            }
                        });
                    });
                </script>
                <div class="input-append">
                  <input id="'.$this->_column.'_imgtf" class="span2" size="16" type="text" name="'.$this->_column.'" value="'.$this->_model->{$this->_column}.'">
                  <span class="add-on"><a href="'.BASE_URL.'public/data/uploads/images/'.$this->_model->{$this->_column}.'" id="'.$this->_column.'_imglink" class="fancybox"><i class="icon-zoom-in"></i></a></span>
                </div><div id="imageUploadButton'.$uniqid.'">Upload</div>'.(!empty($this->_attributes['description']) ? ''.$this->_attributes['description'] : '');