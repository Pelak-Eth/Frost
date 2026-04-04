<div class="modal fade" id="announcement-modal" tabindex="-1" role="dialog" aria-labelledby="announcement-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3>
                    <span id="title"> </span>
                    <small class="text-right">
                        By <span id="user-name"></span>
                    </small>
                    <small class="pull-right">
                        <span><i class="fa fa-calendar"></i>
                            <span id="created-at"> </span>
                        </span>
                    </small>
                </h3>
            </div>
            <div class="modal-body">
                <div id="announcement-content"></div>
                <div id="comments"></div>
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 well">
                        <input id="announcement-id" type="hidden" value="" />
                        <input type="hidden" id="reply">
                        <div data-quill data-quill-target="reply" data-quill-toolbar="compact" style="height:160px"></div>
                        <button id="post-reply" class="btn btn-block btn-raised btn-info">Add Comment</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>