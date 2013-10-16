$(function () {
    
    $('#uploader_att_url').css('display', 'none');
    $('#uploader_att_url').after('<a class="btn btn-primary" onclick="$(\'input[id=uploader_att_url]\').click()"><?= $this->translate('att_uploader_type'.(!empty($this->type) ? '_'.strtolower($this->type) : '').'_button_choose');?></a>');

});