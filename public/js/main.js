$(document).ready(function() {

    $('.cardTypeTabs>ul>li>a').on('click', function(e) {

        e.preventDefault();

        console.log('test');

        var type = $(this).attr('class').replace('Cue', '');

        var id = $(this).attr('id').replace('cue', '');

        var target = type + 'Content' + id;

        $('#createdContent' + id).hide();
        $('#movedContent' + id).hide();

        $('#' + type + 'Content' + id).show();

    });

    $.ajax({
        url: 'getTimelineJSON',
        type: 'POST',
        dataType: 'json',
        data: {
            _token: $('meta[name="csrf-token"').attr('content')
        },
        success: function(data) {
            createTimeline(data);
        }
    });

    function createTimeline(json)
    {
        console.log('creating timeline');
        window.timeline = new TL.Timeline('timeline-embed', json);
    }

});
