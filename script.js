
/*
    Bootstrap Spinner
*/
function spinner_btn_show(btn) {
    var obj = jQuery(btn);
    obj.attr('data-spinner', jQuery('<div>').text(obj.html()).html());
    obj.html('<span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span role="status"> '+jQuery.locale.loading+'</span>');
    obj.attr('disabled', true);
}

function spinner_btn_hide(btn) {
    var obj = jQuery(btn);
    obj.html(jQuery('<div>').html(obj.attr('data-spinner')).text());
    obj.removeAttr('disabled');
}

/*
    Bootstrap Toast
*/
function toast_show(type, message) {
    if (jQuery('.toast-container').length == 0) {
        jQuery('body').append(`
        <div class="position-fixed" style="top:25px; right: 0px; left: 0px;">
            <div aria-live="polite" aria-atomic="true" class="position-relative">
                <div class="toast-container top-0 end-0 p-3">
                </div>
            </div>
        </div>`);
    }
    var css_type = 'text-bg-primary';
    switch (type) {
        case 'success':
            css_type = 'text-bg-success'; break;
        case 'danger':
            css_type = 'text-bg-danger'; break;
        case 'warning':
            css_type = 'text-bg-warning'; break;
    }
    var obj = jQuery(`
        <div class="toast align-items-center `+css_type+` border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">`+message+`</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>`);
    jQuery('.toast-container').append(obj);
    obj.toast('show');
    obj.on('hidden.bs.toast', function() { obj.remove(); });
}