function ajax(url, data = {}, success) {
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        success: success,
        error: function() {
            console_log("Ошибка соединения");
        }
    });
}