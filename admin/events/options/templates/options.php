<script defer>
    new Noty({
        type: 'success',
        layout: 'bottomRight',
        closeWith: ['click', 'button'],
        text: 'Some notification text',
        timeout: 2500
    }).show();
    new Noty({
        type: 'warning',
        layout: 'bottomRight',
        text: 'Some notification text',
        timeout: 2500
    }).show();
    new Noty({
        type: 'error',
        layout: 'bottomRight',
        text: 'Some notification text',
        timeout: 5000
    }).show();
</script>