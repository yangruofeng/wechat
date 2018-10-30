$("input[name=test]").click(function(){
    var table = $(this).parents("table[action]");

    var params = []
    table.find("input").each(function(){
        var field = $(this).attr("name");
        if (field != "test") {
            var value = $(this).val();
            params.push(field + "=" + encodeURIComponent(value));
        }
    });

    var url = table.attr("action");
    if (url.indexOf("?") != -1)
        url += "&" + params.join("&");
    else
        url += "?" + params.join("&");

    table.find("*[name=url]").text(url);

    var response_area = table.find("*[name=response]");
    response_area.text("Requesting ...");

    $.ajax({
        url: url,
        complete: function(xhr, status) {
            if (status == "success") {
                response_area.text(xhr.responseText);
            } else {
                response_area.text("Response status: " + status);
            }
        }
    });
});