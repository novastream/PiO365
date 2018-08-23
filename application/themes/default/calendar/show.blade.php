<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
@push('inline_styles')
@endpush

@include('partials.app_header')

<div class="p-3"></div>

@if($settings->layout == 'boxed')
<div class="container">
@endif

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
        @if($showHelp == 'config')
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        Configuration is incomplete. Please go to the <a href="{{site_url('config')}}">configuration area</a>.
                    </div>
                </div>
            </div>
        @endif
        @if($showHelp == 'signin')
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        You are not logged in. Please <a href="{{site_url('authentication/signin/calendar')}}">sign in</a> to start displaying events.
                    </div>
                </div>
            </div>
        @endif
    @endif

    <div class="row">

        <div class="col-md-12">
            
            <div id="calendar"></div>

        </div>

    </div>

@if($settings->layout == 'boxed')
</div>
@endif

@push('inline_scripts')
    <script>
        (function(app) {
            app(window.jQuery, window, document);
        }(function($, window, document) {
            $(function() {

                var base_url = '{{base_url()}}';
                var site_url = '{{site_url()}}';

                $('#calendar').fullCalendar({
                    locale: '{!!$settings->locale!!}',
                    defaultView: '{!!$settings->default_view!!}',
                    views: {
                        basic: {
                            timeFormat: '{!!$settings->time_format!!}'
                        },
                        agenda: {
                            timeFormat: '{!!$settings->time_format!!}'
                        },
                        week: {
                            timeFormat: '{!!$settings->time_format!!}'
                        },
                        day: {
                            timeFormat: '{!!$settings->time_format!!}'
                        }
                    },
                    slotLabelFormat: '{!!$settings->time_format!!}',
                    timeFormat: '{!!$settings->time_format!!}',

                    @if($settings->week_number)
                    weekNumbers: true,
                    @endif

                    themeSystem: '{!!$settings->theme!!}',
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },

                    @if($settings->locale == 'sv')
                        buttonText: {
                            today:    'Idag',
                            month:    'Månad',
                            week:     'Vecka',
                            day:      'Dag',
                            list:     'Lista'
                        },
                        allDayText: 'Hela dagen',
                    @endif

                    timezone: '{{$settings->calendar_timezone}}',

                    events: function(start, end, timezone, callback) {
                        $.ajax({
                            url: site_url + 'calendar/get',
                            dataType: 'json',
                            cache: true,
                            data: {
                                start: start.format("YYYY-MM-DD"),
                                end: end.format("YYYY-MM-DD")
                            },
                            success: function(response) {
                                
                                var events = [];
                                
                                if (response.status == 'success') {
                                    $.map( response.data, function( data ) {
                                        events.push({
                                            id: data.id,
                                            title: data.title,
                                            start: data.start,
                                            end: data.end                                            
                                        });
                                    });
                                }

                                callback(events);

                            }
                        });
                    },
                    eventRender: function(event, element) {                                          
                        
                    }
                });
                
                setInterval(function() {
                    $('#calendar').fullCalendar( 'refetchEvents' );
                }, 300 * 1000);
            });
        }));
    </script>
@endpush

@include('partials.app_footer')