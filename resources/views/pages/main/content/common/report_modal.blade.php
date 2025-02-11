<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Report An Issue</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="reportForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="reasonForAccess">Describe briefly about the issue with the current {{$text}}.</label>
                            <textarea class="form-control" id="reportMessage" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            {!! NoCaptcha::display(['data-callback' => 'capcthaCallback2', 'data-expired-callback' => 'capcthaExpired2']) !!}
                            <input type="hidden" id="captcha_response2" value="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="SubmitBtn2">Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
