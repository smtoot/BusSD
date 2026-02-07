<div class="code-box-wrapper d-flex w-100">
    <div class="form-group mb-3 flex-fill">
        <span class="text-white">@lang('Verification Code')</span>
        <div class="verification-code">
            <input type="text" name="code" class="overflow-hidden" autocomplete="off">
            <div class="boxes">
                <span>-</span>
                <span>-</span>
                <span>-</span>
                <span>-</span>
                <span>-</span>
                <span>-</span>
            </div>
        </div>
    </div>
</div>

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/verification_code.css') }}">
@endpush

@push('script')
    <script>
        $('[name=code]').on('input', function() {
            $(this).val(function(i, val) {
                if (val.length == 6) {
                    $('form').find('button[type=submit]').html('<i class="las la-spinner fa-spin"></i>');
                    $('form').find('button[type=submit]').removeClass('disabled');
                    $('form')[0].submit();
                } else {
                    $('form').find('button[type=submit]').addClass('disabled');
                }
                if (val.length > 6) {
                    return val.substring(0, val.length - 1);
                }
                return val;
            });

            for (let index = $(this).val().length; index >= 0; index--) {
                $($('.boxes span')[index]).html('');
            }
        });
    </script>
@endpush
