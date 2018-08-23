<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
@push('inline_styles')
@endpush

@include('partials.app_header')

<div class="p-3"></div>

<div class="container">

    <div class="row">

        <div class="col-md-12">
            
            <h4>Configuration</h4>

            @if(isset($response['status']))
                @if($response['status'] == 'success')
                    @if(is_array($response['message']))
                        @foreach ($response['message'] as $message)
                            @if(!empty($message))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    {{$message}}
                                </div>
                            @endif
                        @endforeach
                    @else
                        @if(!empty($response['message']))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                {{$response['message']}}
                            </div>
                        @endif
                    @endif
                @else
                    @if(is_array($response['message']))
                        @foreach ($response['message'] as $message)
                            @if(!empty($message))
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    {{$message}}
                                </div>
                            @endif
                        @endforeach
                    @else
                        @if(!empty($response['message']))
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                {{$response['message']}}
                            </div>
                        @endif
                    @endif
                @endif
            @endif

            @if(!empty($showHelp))
                @if($showHelp == 'signin')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                You are not logged in. Please <a href="{{site_url('authentication/signin/config')}}">sign in</a> to start displaying events.
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            {!!form_open(site_url('config/save'), array('id' => 'ConfigForm'))!!}

                <div class="form-group">
                    <label for="layout">Layout</label>
                    <select class="form-control select2-no-search" id="layout" name="layout">
                        <option value="full"@if($settings->layout == 'full') selected @endif>Full width</option>
                        <option value="boxed"@if($settings->layout == 'boxed') selected @endif>Boxed</option>
                    </select>
                    <small id="layoutHelp" class="form-text text-muted">Full width will make the calendar as big as your screen while boxed will use a Bootstrap 4 container with 12 columns.</small>
                </div>

                <div class="form-group">
                    <label for="theme">Theme</label>
                    <select class="form-control select2-no-search" id="theme" name="theme">
                        <option value="standard"@if($settings->theme == 'standard') selected @endif>Standard</option>
                        <option value="bootstrap4"@if($settings->theme == 'bootstrap4') selected @endif>Bootstrap 4</option>
                    </select>
                    <small id="themeHelp" class="form-text text-muted">Calendar theme will change the look of the calendar.</small>
                </div>

                <div class="form-group">
                    <label for="locale">Locale</label>
                    <select class="form-control select2-no-search" id="locale" name="locale">
                        <option value="en"@if($settings->locale == 'en') selected @endif>English</option>
                        <option value="sv"@if($settings->locale == 'sv') selected @endif>Swedish</option>
                    </select>
                    <small id="localeHelp" class="form-text text-muted">Locale will decide localization and language</small>
                </div>

                <div class="form-group">
                    <label for="default_view">Default view</label>
                    <select class="form-control select2-no-search" id="default_view" name="default_view">
                        <option value="month"@if($settings->default_view == 'month') selected @endif>Month</option>
                        <option value="agendaWeek"@if($settings->default_view == 'agendaWeek') selected @endif>Week</option>
                        <option value="agendaDay"@if($settings->default_view == 'agendaDay') selected @endif>Day</option>
                    </select>
                    <small id="default_viewHelp" class="form-text text-muted">Month view will display a whole month, week view will display a week in agenda view and a day will display a day in agenda view.</small>
                </div>

                <div class="form-group">
                    <label for="week_number">Show week numbers</label>
                    <select class="form-control select2-no-search" id="week_number" name="week_number">
                        <option value="true"@if($settings->week_number == true) selected @endif>Yes</option>
                        <option value="false"@if($settings->week_number == false) selected @endif>No</option>
                    </select>
                    <small id="week_numberHelp" class="form-text text-muted">Show week number to the left of the calendar</small>
                </div>

                <div class="form-group">
                    <label for="time_format">Time format</label>
                    <input type="text" class="form-control" id="time_format" name="time_format" aria-describedby="time_formatHelp" value="{{$settings->time_format}}" autocomplete="off">
                    <small id="time_formatHelp" class="form-text text-muted"><a href="https://momentjs.com/docs" target="_blank">Take me to the Moment documentation</a></small>
                </div>

                <div class="form-group">
                    <label for="calendar_timezone">Timezone</label>
                    <select class="form-control select2" id="calendar_timezone" name="calendar_timezone">
                        @foreach ($timezones as $timezone)
                            @if($settings->calendar_timezone == $timezone['zone'])
                                <option value="{{$timezone['zone']}}" selected>{{$timezone['diff_from_GMT']}} - {{$timezone['zone']}}</option>
                            @else 
                                <option value="{{$timezone['zone']}}">{{$timezone['diff_from_GMT']}} - {{$timezone['zone']}}</option>
                            @endif
                        @endforeach
                    </select>
                    <small id="calendar_timezoneHelp" class="form-text text-muted"></small>
                </div>

                <div class="form-group">
                    <label for="oauth_app_id">Oauth App ID</label>
                    <input type="text" class="form-control" id="oauth_app_id" name="oauth_app_id" aria-describedby="oauth_app_idHelp" value="{{$settings->OAUTH_APP_ID}}" autocomplete="off">
                    <small id="oauth_app_idHelp" class="form-text text-muted">Your Office 365 APP ID. <a href="https://apps.dev.microsoft.com" target="_blank">Create a new app</a></small>
                </div>

                <div class="form-group">
                    <label for="oauth_app_password">Oauth App Password</label>
                    <input type="password" class="form-control" id="oauth_app_password" name="oauth_app_password" aria-describedby="oauth_app_passwordHelp" autocomplete="off">
                    <small id="oauth_app_passwordHelp" class="form-text text-muted">Your Office 365 APP Password (will be hidden after save)</small>
                </div>

                <div class="form-group">
                    <label for="oauth_redirect_uri">Oauth Redirect Uri</label>
                    <input type="text" class="form-control" id="oauth_redirect_uri" name="oauth_redirect_uri" aria-describedby="oauth_redirect_uriHelp" value="{{$settings->OAUTH_REDIRECT_URI}}" autocomplete="off">
                    <small id="oauth_redirect_uriHelp" class="form-text text-muted">The same uri as when you created your app in Office 365 <code>{!!site_url('authentication/authorize')!!}</code></small>
                </div>

                <div class="form-group">
                    <label for="app_password">Password</label>
                    <input type="password" class="form-control" id="app_password" name="app_password" aria-describedby="app_passwordHelp" autocomplete="off">
                    <small id="app_passwordHelp" class="form-text text-muted">Password protect this page <code>{!!site_url('config')!!}</code> (will be hidden after save)</small>
                </div>

                <div class="form-group">
                    <label for="calendar">Calendar</label>
                    <select class="form-control select2" id="calendar" name="calendar"@if(empty($calendars)) disabled @endif>
                        @foreach($calendars as $calendar)
                            @if($calendar->id == $settings->default_calendar)
                                <option value="{{$calendar->id}}" selected>{{$calendar->name}}</option>
                            @else 
                                <option value="{{$calendar->id}}">{{$calendar->name}}</option>
                            @endif
                        @endforeach
                    </select>
                    <small id="calendarHelp" class="form-text text-muted">Will populate with availible calendars once you have authenticated</small>
                </div>

                <a href="{{site_url()}}" class="btn btn-secondary mb-2">Go back</a>
                
                @if(!empty($settings->app_password))
                <a href="{{site_url('config/logout')}}" class="btn btn-info mb-2">Logout</a>
                @endif

                @if($codeigniter->office365->getAccessToken() != false)
                    <a href="{{site_url('config/dissconnect')}}" class="btn btn-info mb-2">Dissconnect</a>
                @endif

                <button type="button" class="btn btn-primary mb-2" id="save-btn">Save</button>

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

                $( '.select2' ).select2({
                    language: 'sv',
                    width: '100%'                    
                });

                $( '.select2-no-search' ).select2({
                    language: 'sv',
                    width: '100%',
                    minimumResultsForSearch: -1
                });

                $( '#save-btn' ).click(function(event) {
                    event.preventDefault();
                    $(this).attr('disabled', true);
                    $('#ConfigForm').submit();
                });
                
            });
        }));
    </script>
@endpush

@include('partials.app_footer')