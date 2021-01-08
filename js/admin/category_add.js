$('form').validate({
    onkeyup: false,
    rules: {
        name: {
            required: true,
            maxlength: 100,
        }
    },
    messages: {
        name: {
            required: 'Category name cannot be blank',
        }
    }
});

