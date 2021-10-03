export default class TaskCard {
    constructor() {
        this.$document = $(document);
        this.$modal = $('#js-modal_task-bar');

        this._initEvents();
    }

    _initEvents() {
        this._deleteTaskEvent();
    }

    _reloadPage() {
        window.location.reload();
    }

    _deleteTaskEvent() {
        this.$document.on('click', '.js-delete-task', (e) => {
            const _this = $(e.currentTarget)
            const taskId = _this.data('task');
            
            this._deleteTask(taskId);
        });
    }

    _deleteTask(taskId) {
        $.post(`/tasks/ajax-delete/${taskId}`, () => {
            this._reloadPage();
        });
    }
}