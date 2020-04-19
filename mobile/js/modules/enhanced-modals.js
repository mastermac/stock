
/*
    Enhanced Bootstrap Modals
    https://mdbootstrap.com
    office@mdbootstrap.com
*/

$('body').on('shown.bs.modal', '.modal', function() {
    if($('.modal-backdrop').length) {
    } else {

        $modal_dialog = $(this).children('.modal-dialog')

        if($modal_dialog.hasClass('modal-side')) {
            $(this).addClass('modal-scrolling');
            $('body').addClass('scrollable');
        }

        if($modal_dialog.hasClass('modal-frame')) {
            $(this).addClass('modal-content-clickable');
            $('body').addClass('scrollable');
        }
    }
});
$('body').on('hidden.bs.modal', '.modal', function() {
    $('body').removeClass('scrollable');
});
"use strict";

(function ($) {
  $('.input-default-wrapper').on('change', '.input-default-js', function (e) {
    var $this = $(e.target),
        $label = $this.next('label'),
        $files = $this[0].files;
    var fileName = '';

    if ($files && $files.length > 1) {
      fileName = ($this.attr('data-multiple-target') || '').replace('{target}', $files.length);
    } else if (e.target.value) {
      fileName = e.target.value.split('\\').pop();
    }

    if (fileName) {
      $label.find('.span-choose-file').html(fileName);
    } else {
      $label.html($label.html());
    }
  });
})(jQuery);