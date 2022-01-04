import Preloader from "../../components/Preloader";

export default class EditModal {
    constructor() {
        this.$document = $(document);
        this.$modal = $('#js-modal_task-bar');

        this._state();
        this._initEvents();
    }

    _initEvents() {
        this._editEvent();
        this._modalSubmitEditEvent();
        this._addTaskToUserEvent();
        this._modalSubmitAdditionEvent();
    }

    _state() {
        this.state =  {
            taskId: null,
            userId: null,
        };
    }

    _preloader() {
        return Preloader.pinwheelSvg();
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
                this._reloadPage();
            }

            this.$modal
                .find('.modal-title').html(data.formTitle);
            this.$modal
                .find('.modal-body__content').html(data.form);
            this.$modal
                .find('.modal-footer').html(data.formButtons);
        });
    }

    _showAdditionForm() {
        $.post(`/tasks/user/${this.state.userId}/add-by-modal`, (data) => {
            if (! data.form) {
                this._reloadPage();
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

    _sendAddForm(formData) {
        $.post(`/tasks/user/${this.state.userId}/add-by-modal`, formData, () => {
            this._reloadPage();
        });
    }

    _beforeShowModal() {
        this.$modal
            .find('.modal-body__error').html('');
        this.$modal
            .find('.modal-body__content').html(this._preloader());
    }

    _editEvent() {
        this.$document.on('click', '.js-modal-edit', (e) => {
            const _this = $(e.currentTarget);
            this.state.taskId = _this.parents('.card').data('task');

            this._beforeShowModal()
            this._showModal();
            this._showEditForm();
        });
    }

    _modalSubmitEditEvent() {
        this.$document.on('click', '.js-modal-submit__edit', () => {
            const formData = this.$modal.find('form').serializeArray();
            this._sendEditForm(formData);
        });
    }

    _addTaskToUserEvent() {
        this.$document.on('click', '#js-add-task', (e) => {
            const _this = $(e.currentTarget);
            this.state.userId = _this.data('user');

            this._beforeShowModal();
            this._showModal();
            this._showAdditionForm();
        });
    }

    _modalSubmitAdditionEvent() {
        this.$document.on('click', '.js-modal-submit__add', () => {
            const formData = this.$modal.find('form').serializeArray();
            this._sendAddForm(formData);
        });
    }
}