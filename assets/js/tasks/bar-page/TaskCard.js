export default class TaskCard {
    constructor() {
        this.$document = $(document);
        this.$modal = $('#js-modal_task-bar');

        this._initEvents();
    }

    _initEvents() {
        this._deleteTaskEvent();
        this._fulfilledTaskEvent();
        this._publishedTaskEvent();
    }

    _reloadPage() {
        window.location.reload();
    }

    _deleteTaskEvent() {
        this.$document.on('click', '.js-delete-task', (e) => {
            const _this = $(e.currentTarget)
            const taskId = _this.parents('.card').data('task');
            
            this._deleteTask(taskId);
        });
    }

    _fulfilledTaskEvent() {
        this.$document.on('click', '.js-fulfilled-task', (e) => {
            const _this = $(e.currentTarget)
            const taskId = _this.parents('.card').data('task');

            this._fulfilledTask(taskId);
        });
    }

    _publishedTaskEvent() {
        this.$document.on('click', '.js-published-task', (e) => {
            const _this = $(e.currentTarget)
            const taskId = _this.parents('.card').data('task');

            this._publishedTask(taskId);
        });
    }

    _deleteTask(taskId) {
        $.post(`/tasks/ajax-delete/${taskId}`, () => {
            this._reloadPage();
        });
    }

    _fulfilledTask(taskId) {
        $.post(`/tasks/ajax-fulfilled/${taskId}`, () => {
            this._reloadPage();
        });
    }

    _publishedTask(taskId) {
        $.post(`/tasks/ajax-published/${taskId}`, (data) => {
            this._reloadPage();
        });
    }
}