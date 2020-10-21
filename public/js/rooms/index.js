/**
 * An object for managing tasks related to rooms
 */
function Room(url, resourceName) {
    Resource.call(this, url, resourceName);
}

App.extend(Resource, Room);

Room.prototype.init = function () {
    var self = this;
    Resource.prototype.init.call(self);

    $(document).on('change', '#block-select', function () {
        var blockId = $(this).val();

        self.loadBuildingRooms(blockId);
    });

};

Room.prototype.prepareForUpdate =  function(resource) {
    self.loadingForUpdate = true;

    $('input[name=name]').val(resource.name);
    $('input[name=capacity]').val(resource.capacity);
    $('select[name=block_id]').val(resource.block_id).change();

    this.loadBuildingRooms(resource.block_id, resource.adjacent_room_ids);
};

Room.prototype.refreshPage = function (keyword) {
    var self = this;

    if (!keyword) {
        location.reload();
    } else {
        Resource.prototype.refreshPage.call(self, keyword);
    }
};

Room.prototype.loadBuildingRooms = function(blockId, selected = null) {
    var url = '/rooms?block_id=' + blockId + '&select_view=true'
    var $container = $('#rooms');

    if (self.loadingForUpdate) {
        self.loadingForUpdate = false;
        return false;
    }

    $.ajax({
        type: 'get',
        url: url,
        success: function (response) {
            $container.html(response);

            $container.find(".select2").select2({
                allowClear: true
            });

            if (selected) {
                $container.find('select').val(selected).change();
            }
        }
    });
};

window.addEventListener('load', function(){
    var room = new Room('/rooms', 'Lecture Room');
    room.init();
});