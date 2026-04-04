@extends('master')
@section('title', 'Dashboard')
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h2><i class="fa fa-cogs" aria-hidden="true"></i> Account Information</h2>
                </div>
                <div class="panel-body">
                    <table class="table table-hover">
                        <tr>
                            <td>Username</td>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <td>E-mail</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                    </table>
                    <a href="/account/edit" class="btn btn-raised btn-warning btn-block">
                        <i class="fa fa-cog" aria-hidden="true"></i> Edit Account Information
                    </a>
                </div>
            </div>
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h2><i class="fa fa-calendar" aria-hidden="true"></i> Scheduled Shifts</h2>
                </div>
                <div class="panel-body">
                    @if($shifts)
                        <table class="table table-hover">
                            <thead>
                            <th>Date</th>
                            <th>Store</th>
                            </thead>
                            <tbody>
                            @foreach($shifts as $shift)
                                <tr>
                                    <td>
                                        {{ date('l F jS, h:ia', strtotime($shift->start)) }} -
                                        {{ date('h:i:a', strtotime($shift->end)) }}
                                    </td>
                                    <td>{{ config('store.stores')[$shift->store] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <h3>You are not on the schedule for this week.</h3>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h2>
                        <i class="fa fa-newspaper-o" aria-hidden="true"></i> Announcements
                        <a href="/announcements/create" class="btn btn-success btn-raised pull-right">
                            <i class="fa fa-plus-square" aria-hidden="true"></i> Create New Announcement
                        </a>
                    </h2>
                </div>
                <div class="panel-body">
                    @include('account.partials.announcement-list')

                </div>
            </div>
        </div>
    </div>
    @include('account.partials.announcement-modal')
@endsection

@push('scripts')
    @vite('resources/js/quill.js')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            }
        });


        $('.read-more').on('click', function () {
            var id = $(this).attr('data-id');
            $.getJSON('/announcements/' + id + '/show', function(data) {
                var anncmnt = data;
                $('#title').html(anncmnt.title);
                $('#user-name').html(anncmnt.user);
                $('#announcement-content').html(anncmnt.content);
                $('#created-at').html(anncmnt.created);
                $('#announcement-id').val(id);
                $('#comments').html('');
                $.each(anncmnt.comments, function(key, value) {
                    $('#comments').append('<div class="well"><p>' + value.content + '</p><h6>By: ' + value.user + ' - ' + value.date + '</h6></div>')
                })
            });
            $('#announcement-modal').modal('show');
        });
        $('#post-reply').on('click', function() {
            var replyContent = $('#reply').val();
            $.ajax({
                url: '/announcements/' + $('#announcement-id').val() + '/add-comment',
                type: "POST",
                dataType: "json",
                data: {
                    'content' : replyContent
                }
            }).done(function( json ) {
                if(json == 'success') {
                    $('#comments').append('<div class="well">' + replyContent + '</div>');
                    var replyEditor = document.querySelector('[data-quill-target="reply"]');
                    if (replyEditor && replyEditor.__quill) {
                        replyEditor.__quill.setContents([]);
                    }
                    $('#reply').val('');
                }
            });
        })
    </script>
@endpush

@push('css')
    <style>
        .modal-dialog {
            width: 70%;
        }
    </style>
@endpush