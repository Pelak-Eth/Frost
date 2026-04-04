@extends('master')
@section('title', 'Edit Announcement')
@section('content')
    <div class="container col-md-8 col-md-offset-2">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h2>Edit announcement</h2>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post">
                    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                    <div class="form-group">
                        <label for="title" class="col-lg-2 control-label">Title</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" placeholder="Announcement Title" name="title" value="{{ $announcement->title }}">
                            <span class="help-block">The title of the announcement.</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Type</label>
                        <div class="col-lg-10">
                            @foreach(config('store.announcement_types') as $type => $info)
                                <label class="radio-inline">
                                    <input type="radio" name="type" value="{{ $type }}" @if($announcement->type == $type)checked @endif >
                                    <i class="{{ $info['icon'] . ' ' . $info['color'] }}" aria-hidden="true"></i>
                                    {{ ucfirst($type) }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @if(Auth::user()->hasRole('admin'))
                        <div class="form-group">
                            <label for="sticky" class="col-lg-2 control-label">Sticky?</label>
                            <div class="togglebutton col-lg-10">
                                <label>
                                    <input type="checkbox" name="sticky" @if($announcement->sticky)checked @endif >
                                    <span class="help-block">Stickied posts will always show up at the top of the list.</span>
                                </label>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-lg-8 col-lg-offset-2">
                            <h4>Content</h4>
                            <input type="hidden" name="content" id="content" value="{{ old('content', $announcement->content) }}">
                            <div data-quill data-quill-target="content" data-quill-toolbar="full" style="height:280px"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-5 col-lg-offset-7">
                            <a href="/" class="btn btn-default btn-raised">Cancel</a>
                            <a href="/announcements/{{ $announcement->id }}/delete" class="btn btn-raised btn-danger">Delete</a>
                            <button type="submit" class="btn btn-primary btn-raised">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/quill.js')
@endpush
