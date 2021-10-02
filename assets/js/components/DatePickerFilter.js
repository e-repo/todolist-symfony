// Import lodash merge
import merge from 'lodash/merge'

export default class DatePickerFilter {

    /**
     * [
     *      {name: 'some class name', options: 'Date picker options'},
     *      {name: 'some class name', options: 'Date picker options'},
     *      ...
     * ]
     * @param config
     */
    constructor(config = {}) {
        this._init(config);
    }

    _defaultDatePickerOptions() {
        return {
            options: {
                'zIndexOffset': 9999,
                'format': 'dd.mm.yyyy'
            }
        };
    }

    _init(config) {
        for (let item of config) {
            const resolvedItem = merge(item, this._defaultDatePickerOptions());
            this._initDatePicker(resolvedItem)
        }
    }

    _initDatePicker(resolvedItem) {
        $(`.${resolvedItem.name}`).datepicker(resolvedItem.options);
    }
}