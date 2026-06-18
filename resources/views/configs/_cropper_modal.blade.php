{{-- Cropper.js CDN --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
@endpush

{{-- Modal HTML --}}
<div id="logo-crop-modal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" id="crop-backdrop"></div>
    <div class="absolute inset-0 flex items-center justify-center p-8">
        {{-- overflow-hidden wajib agar Cropper.js tidak bleed ke luar card --}}
        <div class="relative z-10 flex w-full max-w-2xl flex-col overflow-hidden rounded-xl border bg-card shadow-2xl">
            {{-- Header --}}
            <div class="flex shrink-0 items-center justify-between border-b px-6 py-4">
                <h3 class="text-sm font-semibold">@lang('general.logo_crop_title')</h3>
                <button type="button" id="crop-close-btn" class="rounded p-1 text-muted-foreground hover:text-foreground">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            {{-- Crop area — overflow:hidden clips Cropper canvas, height drives the area size --}}
            <div id="crop-container" style="position:relative;overflow:hidden;background:#09090b;height:50vh;">
                <img id="crop-img" src="" alt="" style="display:block;max-width:100%;">
            </div>
            {{-- Footer --}}
            <div class="flex shrink-0 items-center justify-end gap-3 border-t px-6 py-4">
                <button type="button" id="crop-cancel-btn" class="btn btn-outline btn-sm">@lang('general.cancel')</button>
                <button type="button" id="crop-apply-btn" class="btn btn-primary btn-sm">
                    <x-icon name="check" class="size-3.5" />
                    @lang('general.logo_apply')
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
(function () {
    let cropperInstance = null;
    let activeCard      = null;

    document.querySelectorAll('.logo-file-trigger').forEach(trigger => {
        trigger.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            activeCard = this.closest('.logo-upload-card');
            const aspectRatio = parseFloat(activeCard.dataset.aspect);
            const reader = new FileReader();
            reader.onload = e => openCropModal(e.target.result, aspectRatio);
            reader.readAsDataURL(file);
            this.value = '';
        });
    });

    function openCropModal(src, aspectRatio) {
        const modal = document.getElementById('logo-crop-modal');
        const img   = document.getElementById('crop-img');

        if (cropperInstance) { cropperInstance.destroy(); cropperInstance = null; }
        img.src = '';
        modal.classList.remove('hidden');

        requestAnimationFrame(() => requestAnimationFrame(() => {
            img.onload = () => {
                cropperInstance = new Cropper(img, {
                    aspectRatio:  aspectRatio,
                    viewMode:     1,
                    autoCropArea: 1,
                    movable:      true,
                    zoomable:     true,
                    rotatable:    false,
                    scalable:     false,
                    background:   false,
                });
            };
            img.src = src;
        }));
    }

    function closeCropModal() {
        document.getElementById('logo-crop-modal').classList.add('hidden');
        if (cropperInstance) { cropperInstance.destroy(); cropperInstance = null; }
        activeCard = null;
    }

    document.getElementById('crop-cancel-btn').addEventListener('click', closeCropModal);
    document.getElementById('crop-close-btn').addEventListener('click', closeCropModal);
    document.getElementById('crop-backdrop').addEventListener('click', closeCropModal);

    document.getElementById('crop-apply-btn').addEventListener('click', () => {
        if (!cropperInstance || !activeCard) return;
        const isMini  = parseFloat(activeCard.dataset.aspect) === 1;
        const maxSize = isMini ? 512 : 1920;
        const canvas  = cropperInstance.getCroppedCanvas({ maxWidth: maxSize, maxHeight: maxSize });

        canvas.toBlob(blob => {
            const key  = activeCard.dataset.key;
            const file = new File([blob], key + '.jpg', { type: 'image/jpeg' });

            const dt = new DataTransfer();
            dt.items.add(file);
            activeCard.querySelector('.logo-hidden-file').files = dt.files;

            const previewImg   = activeCard.querySelector('.logo-preview-img');
            const previewEmpty = activeCard.querySelector('.logo-preview-empty');
            if (previewEmpty) previewEmpty.classList.add('hidden');
            previewImg.src = canvas.toDataURL('image/jpeg', 0.85);
            previewImg.classList.remove('hidden');

            closeCropModal();
        }, 'image/jpeg', 0.85);
    });
})();
</script>
@endpush
