function checkAccept(object) {
    if ($(object).is(":checked")) {
        $('#save').attr('disabled', false);
    } else {
        $('#save').attr('disabled', true);
    }
}

function confirmDelete(name) {
    if (confirm("Вы подтверждаете удаление " + name + "?")) {
        return true;
    } else {
        return false;
    }
}

function showContentPage(object, item) {
    if ($(object).val() == 1) {
        $.ajax({
            type: "POST",
            url: "/menu/contentpagelist",
            data: "item=" + item,
            success: function (html) {
                $("#contentPageContainer").empty().html(html.html).removeClass('hide');
            }
        });
    } else {
        $("#contentPageContainer").empty().addClass('hide');
    }
}

function showQuestionForm() {
    if ($('#questionForm').css("display") == "none") {
        $('#questionForm').slideDown();
    } else {
        $('#questionForm').css("display", "none");
    }
}

$(document).ready(function () {
    $(".datepicker").datepicker();

    $("#accordion").accordion({
        collapsible: true,
        active: false,
        heightStyle: "content"
    });

    $('div[id^="accordion"]').accordion({
        collapsible: true,
        active: false,
        heightStyle: "content"
    });
});