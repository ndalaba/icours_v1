function insertToUne(src) {
    $('#imgFile').val(src).attr('value', src);
    $('#img')
        .attr('src', url + '/' + $('#imgFile').val())
        .width(228);
    $('#imgFile,#imgUneBtn').hide();
    $('#remove-post-thumbnail,#img').show();
    $('.div-image, .div-fichier').hide();

}

function removeImage(media) {

    $.post(removeImageUrl, {'image': media, '_token': token}, function () {
        var node = document.getElementById(media);
        node.parentNode.removeChild(node);
    });
    return false;
};
function removeFichier(media) {
    $.post(removeFichierUrl, {'image': media, '_token': token}, function () {
        var node = document.getElementById(media);
        node.parentNode.removeChild(node);
    });
    return false;
};

function removeImg() {
    var input = $('#imgFile');
    input.replaceWith(input.val('').clone(true));
    input.show();
    $('#imgUneBtn').show();
    $('#remove-post-thumbnail,#img').hide();
}
$(document).ready(function () {

    $(".image").on('click', function () {
        $('.div-image').show();
        return false;
    });
    $(".fichier").on('click', function () {
        $('.div-fichier').show();
        return false;
    });
    $(".close").on('click', function () {
        $('.div-image, .div-fichier').hide();
        return false;
    });


    $("ul li a.upload").on('click', function () {
        var url = $(this).attr('href');
        $('.uploadForm').show();
        $('.uploadForm #upload').attr('action', url);
        return false;
    });

    $(".closeUpload").on('click', function () {
        $('#wp-link-wrap').hide();
        window.location.reload();
        return false;
    });

});
