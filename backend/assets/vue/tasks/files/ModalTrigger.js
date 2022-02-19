export default class ModalTrigger {
    constructor(modalSelector) {
        this.modalSelector = modalSelector;
        this._initEvents();
    }

    _initEvents() {
        this._modalShowEvent();
    }

    _modalShowEvent() {
        const filesBtn = $('.js-documents');

        filesBtn.on('click', (e) => {
            const card = $(e.currentTarget).parents('.card');
            const taskId = card.data('task');

            $(this.modalSelector).modal('show');
            this._emitModalShow(taskId);
        });
    }

    _emitModalShow(taskId) {
        const showFilesEvent = new CustomEvent('event:showfiles', {
            detail: {
                taskId
            }
        });
        window.dispatchEvent(showFilesEvent);
    }
}