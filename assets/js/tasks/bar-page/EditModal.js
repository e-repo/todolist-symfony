export default class EditModal {
    constructor() {
        this.$document = $(document);
        this.$modal = $('#js-modal_task-bar');

        this._state();
        this._init();
    }

    _init() {
        this._addEditEvent();
        this._addModalSubmitEvent();
    }

    _state() {
        this.state =  {
            taskId: '',
        };
    }

    _preloader() {
        return '<div class="d-flex justify-content-center"><?xml version="1.0" encoding="utf-8"?>\n' +
            '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: none; display: block; shape-rendering: auto; animation-play-state: running; animation-delay: 0s;" width="75px" height="75px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">\n' +
            '<g transform="translate(50 50)" style="animation-play-state: running; animation-delay: 0s;">\n' +
            '  <g transform="scale(0.7)" style="animation-play-state: running; animation-delay: 0s;">\n' +
            '    <g transform="translate(-50 -50)" style="animation-play-state: running; animation-delay: 0s;">\n' +
            '      <g style="animation-play-state: running; animation-delay: 0s;">\n' +
            '        <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" values="0 50 50;360 50 50" keyTimes="0;1" dur="0.7575757575757576s" style="animation-play-state: running; animation-delay: 0s;"></animateTransform>\n' +
            '        <path fill-opacity="0.8" fill="#321fdb" d="M50 50L50 0A50 50 0 0 1 100 50Z" style="animation-play-state: running; animation-delay: 0s;"></path>\n' +
            '      </g>\n' +
            '      <g style="animation-play-state: running; animation-delay: 0s;">\n' +
            '        <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" values="0 50 50;360 50 50" keyTimes="0;1" dur="1.0101010101010102s" style="animation-play-state: running; animation-delay: 0s;"></animateTransform>\n' +
            '        <path fill-opacity="0.8" fill="#2eb85c" d="M50 50L50 0A50 50 0 0 1 100 50Z" transform="rotate(90 50 50)" style="animation-play-state: running; animation-delay: 0s;"></path>\n' +
            '      </g>\n' +
            '      <g style="animation-play-state: running; animation-delay: 0s;">\n' +
            '        <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" values="0 50 50;360 50 50" keyTimes="0;1" dur="1.5151515151515151s" style="animation-play-state: running; animation-delay: 0s;"></animateTransform>\n' +
            '        <path fill-opacity="0.8" fill="#f9b115" d="M50 50L50 0A50 50 0 0 1 100 50Z" transform="rotate(180 50 50)" style="animation-play-state: running; animation-delay: 0s;"></path>\n' +
            '      </g>\n' +
            '      <g style="animation-play-state: running; animation-delay: 0s;">\n' +
            '        <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" values="0 50 50;360 50 50" keyTimes="0;1" dur="3.0303030303030303s" style="animation-play-state: running; animation-delay: 0s;"></animateTransform>\n' +
            '        <path fill-opacity="0.8" fill="#e55353" d="M50 50L50 0A50 50 0 0 1 100 50Z" transform="rotate(270 50 50)" style="animation-play-state: running; animation-delay: 0s;"></path>\n' +
            '      </g>\n' +
            '    </g>\n' +
            '  </g>\n' +
            '</g>\n' +
            '</svg></div>';
    }

    _showModal() {
        this.$modal.modal('show');
    }

    _reloadPage() {
        window.location.reload();
    }

    _hideModal() {
        this.$modal.modal('hide');
    }

    _showEditForm() {
        $.post(`/tasks/bar/${this.state.taskId}/edit-by-modal`, (data) => {
            if (! data.form) {
                window.location.reload();
            }

            this.$modal
                .find('.modal-title').html(data.formTitle);
            this.$modal
                .find('.modal-body__content').html(data.form);
            this.$modal
                .find('.modal-footer').html(data.formButtons);
        });
    }

    _sendEditForm(formData) {
        $.post(`/tasks/bar/${this.state.taskId}/edit-by-modal`, formData, () => {
            this._reloadPage();
        });
    }

    _beforeShowModal() {
        this.$modal
            .find('.modal-body__error').html('');
        this.$modal
            .find('.modal-body__content').html(this._preloader());
    }

    _addEditEvent() {
        this.$document.on('click', '.js-modal-edit', (e) => {
            const _this = $(e.currentTarget);
            this.state.taskId = _this.data('task');

            this._beforeShowModal()
            this._showModal();
            this._showEditForm();
        });
    }

    _addModalSubmitEvent() {
        this.$document.on('click', '.js-modal-submit', () => {
            const formData = this.$modal.find('form').serializeArray();
            this._sendEditForm(formData);
        });
    }
}