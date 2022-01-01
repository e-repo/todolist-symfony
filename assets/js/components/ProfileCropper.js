// Import cropper js
import Cropper from 'cropperjs';

export default class ProfileCropper {
    constructor(profileImage) {
        this._profileImage = profileImage;
        this._cropper = null;
        this._loadFile = null;

        this._init();
    }

    _init() {
        this._initCropper();
        this._initLoadImage();
        this._initUploadCropperImage();
    }

    _initCropper() {
        this._cropper = new Cropper(this._profileImage, {
            aspectRatio: 1,
        });
    }

    _initLoadImage() {
        const cropperInput = document.querySelector('#cropper-input');

        cropperInput.addEventListener('change', (e) => {
            const fileInput = e.currentTarget;

            this._loadFile = fileInput.files[0];

            if (! this._loadFile) {
                return;
            }

            this._profileImage.dataset.imageName = this._loadFile.name
            this._profileImage.src = URL.createObjectURL(this._loadFile);
            this._cropper.destroy();
            this._initCropper();
        });
    }

    _initUploadCropperImage() {
        const uploadBtn = document.querySelector('#js-upload-cropper-image');

        uploadBtn.addEventListener('click', () => {
            this._cropper.getCroppedCanvas().toBlob((blob) => {
                const formData = new FormData();

                formData.append('cropped-image', blob, this._profileImage.getAttribute('data-image-name'));
                formData.append('user-id', this._profileImage.getAttribute('data-user-id'))

                $.ajax('/profile/image-upload', {
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success(data) {
                        document.location.reload();
                    },
                    error(data) {
                        const response = data.responseJSON;
                        if (response.error === true) {
                            bootbox.alert(response.message);
                        }
                    },
                });
            });
        });
    }
}