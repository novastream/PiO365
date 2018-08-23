<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
@push('inline_styles')
@endpush

@include('partials.app_header')

<div class="p-3"></div>

<div class="container">

    <div class="row">

        <div class="col-md-8 offset-md-2">
            
            <h4>Login</h4>

            @if(isset($response['status']))
                @if($response['status'] == 'success')
                    @if(is_array($response['message']))
                        @foreach ($response['message'] as $message)
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                {{$message}}
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{$response['message']}}
                        </div>
                    @endif
                @else
                    @if(is_array($response['message']))
                        @foreach ($response['message'] as $message)
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                {{$message}}
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{$response['message']}}
                        </div>
                    @endif
                @endif
            @endif

            {!!form_open(site_url('config/doLogin'), array('id' => 'LoginForm'))!!}

                <div class="form-group">
                    <label for="passowrd">Password</label>
                    <input type="password" class="form-control" id="password" name="password" aria-describedby="passwordHelp" autocomplete="off">
                    <small id="passwordHelp" class="form-text text-muted">This page is password protected.</small>
                </div>

                <a href="{{site_url()}}" class="btn btn-secondary mb-2">Go back</a>
                <button type="button" class="btn btn-primary mb-2" id="login-btn">Login</button>

            {!!form_close()!!}

        </div>

    </div>

</div>

<div class="p-3"></div>

@push('inline_scripts')
    <script>
        (function(app) {
            app(window.jQuery, window, document);
        }(function($, window, document) {
            $(function() {

                $( '#login-btn' ).click(function(event) {
                    event.preventDefault();
                    $(this).attr('disabled', true);
                    $('#LoginForm').submit();
                });
                
            });
        }));
    </script>
@endpush

@include('partials.app_footer')