@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body ">
                    <form action="{{ route('owner.ticket.store') }}" enctype="multipart/form-data" method="post"
                        class="form-horizontal disableSubmission">
                        @csrf
                        <div class="row ">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Subject')</label>
                                    <input type="text" name="subject" value="{{ old('subject') }}" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Priority')</label>
                                    <select name="priority" class="form-select select2" data-minimum-results-for-search="-1"
                                        required>
                                        <option value="{{ Status::PRIORITY_HIGH }}">@lang('High')</option>
                                        <option value="{{ Status::PRIORITY_MEDIUM }}">@lang('Medium')</option>
                                        <option value="{{ Status::PRIORITY_LOW }}">@lang('Low')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Message')</label>
                                    <textarea class="form-control" name="message" rows="5" required placeholder="@lang('Enter your message')"></textarea>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <button type="button" class="btn btn--dark addAttachment my-2"> <i class="fas fa-plus"></i>
                                    @lang('Add Attachment') </button>
                                <p class="mb-2"><span class="text--info">@lang('Max 5 files can be uploaded | Maximum upload size is ' . convertToReadableSize(ini_get('upload_max_filesize')) . ' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')</span></p>
                                <div class="row fileUploadsContainer">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn--primary w-100 my-2" type="submit" name="replayTicket"
                                    value="1">
                                    <i class="la la-fw la-lg la-reply"></i> @lang('Submit')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('owner.ticket.index') }}" />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            var fileAdded = 0;
            $('.addAttachment').on('click', function() {
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled', true)
                }
                $(".fileUploadsContainer").append(`
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 removeFileInput">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="file" name="attachments[]" class="form-control" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                            <button type="button" class="input-group-text removeFile bg--danger border--danger"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>
                `)
            });

            $(document).on('click', '.removeFile', function() {
                $('.addAttachment').removeAttr('disabled', true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });
        })(jQuery);
    </script>
@endpush
