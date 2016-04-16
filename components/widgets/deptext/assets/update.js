/**
 * Created by Rauan on 14.04.2016.
 */
function updateTokenContainer(url, container, depTargetid) {
    console.log($('#' + depTargetid).val());
    var event_value = $('#' + depTargetid).val();
    $.post(url,{ event: event_value }).done(function( data ) {
        //var token_data = jQuery.parseJSON(data);
        console.log(data);
        $('#' + container).html(getTaggedTokens(data));
    });
}
function getTaggedTokens(tokens)
{
    var content = "<div>Available tokens:</div>";
    tokens.forEach(function(item,i) {

        content += "<div><div class = 'token-header'>" + item[0] + ":</div>";
        item[1].forEach(function(attr){
            if(i == 0) attr = "R." + attr;
            else attr = "D"+ i + "." + attr;
            content += "<span class='token'>" + attr + "</span>";
        });
        content += "</div>";
    });
    return content;
}