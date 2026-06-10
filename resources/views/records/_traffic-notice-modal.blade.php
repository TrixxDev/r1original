<div class="modal fade records-traffic-notice-modal-wrap" id="traffic-notice-modal" tabindex="-1" role="dialog" aria-labelledby="traffic-notice-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg records-traffic-notice-modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="traffic-notice-modal-label">Piebraukšana uz Ulbroku</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Aizvērt">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0 text-center">
                <img
                    src="{{ asset('images/piebrauciens-ulbroka.png') }}"
                    alt="Satiksmes organizācijas shēma — iebraukšana no Institūta ielas"
                    class="img-fluid records-traffic-notice__map"
                >
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('traffic-notice-modal');
    if (modal && modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }

    function openTrafficNoticeModal(e) {
        if (e) {
            e.preventDefault();
        }
        $('#traffic-notice-modal').modal('show');
    }

    document.querySelectorAll('.records-traffic-notice').forEach(function (el) {
        el.addEventListener('click', openTrafficNoticeModal);
        el.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                openTrafficNoticeModal(e);
            }
        });
    });
});
</script>
