<script>
    $("{{ $validator['selector'] }}").each(function() {
        $(this).validate({
            errorElement: 'span',
            errorClass: 'invalid-feedback',
            errorPlacement: function(error, element) {
                if (element.parent('.input-group').length || element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                    error.insertAfter(element.parent());
                    // else just place the validation message immediately after the input
                } else if ((element.hasClass('select2') || element.hasClass('select2-hidden-accessible')) && element.next('.select2-container').length) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.form-control').removeClass('is-valid').addClass('is-invalid'); // add the Bootstrap error class to the control group
            },
            unhighlight: function(element) {
                $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid');
            },
            success: function(element) {
                    $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid'); // remove the Boostrap error class from the control group
            },
            focusInvalid: false, // do not focus the last invalid input
            @if(isset($validator['ignore']) && is_string($validator['ignore']))
            ignore: "{{ $validator['ignore'] }}",
            @endif
            @if (isset($validator['focus_on_error']) && $validator['focus_on_error'])
            invalidHandler: function(form, validator) {
                if (!validator.numberOfInvalids()) {
                    return;
                }
                $('html, body').animate({
                    scrollTop: $(validator.errorList[0].element).offset().top
                }, {{  $validator['animate_duration']  }});
                $(validator.errorList[0].element).focus();
            },
            @endif
            rules: @json($validator['rules'])
        });
    });
</script>
