@php
    $pgService = app(App\Services\PGService::class);
    $listChannel = $pgService->getListChannelVendor();
@endphp
<div class="modal fade" id="uploadXlsxModal" tabindex="-1" role="dialog" aria-labelledby="uploadXlsxModal"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadXlsxModalLabel">Upload Sale Xlsx</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('sales-upload.storeByXlsx') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" rows="5" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="category_name">Upload File Here <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" name="file_xlsx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
