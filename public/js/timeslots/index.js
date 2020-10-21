/**
 * An object for managing tasks related to timeslots
 */
function Timeslot(url, resourceName) {
    Resource.call(this, url, resourceName);
}

App.extend(Resource, Timeslot);

Timeslot.prototype.init = function() {
    var self = this;
    Resource.prototype.init.call(self);

    $(document).on('change', '#from-select', function() {
        var $el = $(this);
        var period = $el.val();

        if (period) {
            var next = self.getNextPeriod(period);

            $('#to-select').val(next).change();
        }
    });

};

Timeslot.prototype.getNextPeriod = function (period) {
    var parts = period.split(":");
    var hour = Number(parts[0]);
    var min = Number(parts[1]);
    var nextHour = 0;
    var nextMinute = 0;

    if (min == 30) {
        nextHour = hour + 1;
        if (nextHour > 23) {
            nextHour = 0;
        }
    } else {
        nextHour = hour;
        nextMinute = 30;
    }

    var next = ((nextHour < 10) ? '0' : '');
    next += nextHour.toString() + ":" + nextMinute.toString() + ((nextMinute == 0) ? '0' : '');

    return next;
};

Timeslot.prototype.prepareForUpdate = function (resource) {
    $('select[name=from]').val(resource.from).change();
    $('select[name=to]').val(resource.to).change();
    $('select[name=type]').val(resource.type).change();
    $('input[name=label]').val(resource.label);
};

window.addEventListener('load', function () {
    var timeslot = new Timeslot('/timeslots', 'Period');
    timeslot.init();
});