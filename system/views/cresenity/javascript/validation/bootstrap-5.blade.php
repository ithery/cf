<script>
    $("{{ $validator['selector'] }}").each(function() {
        $(this).validate({
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },
            highlight: function (element) {
                $(element).removeClass('is-valid').addClass('is-invalid'); // add the Bootstrap error class to the control group
            },
            @if(isset($validator['ignore']) && is_string($validator['ignore']))
            ignore: "{{ $validator['ignore'] }}",
            @endif

            unhighlight: function(element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            success: function(element) {
                $(element).removeClass('is-invalid').addClass('is-valid'); // remove the Boostrap error class from the control group
            },
            focusInvalid: true, // do not focus the last invalid input
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
