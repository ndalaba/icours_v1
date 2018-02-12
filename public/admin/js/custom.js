/*====================================
 MEDIA JA
 ======================================*/
var insertToUne = function (src) {
    $('#imgFile').val(src).attr('value', src);
    $('#img').attr('src', urlImage + '/' + $('#imgFile').val()).width("100%");
    $('#imgFile,#imgUneBtn').hide();
    $('#remove-post-thumbnail,#img').show();
    $('.div-image, .div-fichier').hide();
}
var removeImage = function (media) {
    $.post(removeImageUrl, {
        'image': media,
        '_token': token
    }, function () {
        var node = document.getElementById(media);
        node.parentNode.removeChild(node);
    });
    return false;
};
var removeFichier = function (media) {
    $.post(removeFichierUrl, {
        'image': media,
        '_token': token
    }, function () {
        var node = document.getElementById(media);
        node.parentNode.removeChild(node);
    });
    return false;
};

var removeImg = function () {
    var input = $('#imgFile');
    input.replaceWith(input.val('').clone(true));
    input.show();
    $('#imgUneBtn').show();
    $('#remove-post-thumbnail,#img').hide();
};

$(document).ready(function () {
$('#wrapper').on('click','a.del',function(e){
    if (!confirm('Êtes vous sûr de vouloir supprimer cet élément?')) e.preventDefault();
});

    var urlImage = '/uploads/images';
    /*====================================
     METIS MENU
     ======================================*/
    $('#main-menu').metisMenu();
    /*======================================
     LOAD APPROPRIATE MENU BAR ON SIZE SCREEN
     ========================================*/
    $(window).bind("load resize", function () {
        if ($(this).width() < 768) {
            $('div.sidebar-collapse').addClass('collapse')
        } else {
            $('div.sidebar-collapse').removeClass('collapse')
        }
    });
    //Set active menu
    var activeMenu = function () {
        var menu = ['home','corriges', 'articles', 'medias', 'etudes', 'users', 'reglages', 'cours'];
        menu.forEach(function (value) {
            if ($('#page-wrapper .' + value).length) {
                $('#main-menu li a').removeClass('active-menu');
                $('#main-menu li ul').removeClass('in');
                $('#main-menu #' + value).addClass('active-menu');
                $('#main-menu #' + value).parent('li').find('ul').addClass('in');
            }
        });
    };
    activeMenu();
    // select all
    $('#select_all').change(function () {
        var checkboxes = $(this).closest('form').find(':checkbox');
        if ($(this).is(':checked')) {
            checkboxes.prop('checked', true);
        } else {
            checkboxes.prop('checked', false);
        }
    });

    $("#main-menu a.upload").on('click', function () {
        var url = $(this).attr('href');
        $('#uploadModal').modal('show');
        $('#upload').attr('action', url);
        return false;
    });

    $(".closeUpload").on('click', function () {
       $('#uploadModal').modal('hide');
        return false;
    });
    $('#imageModal').on('show.bs.modal', function (event) {
        var modal= $(this);
        $.get('/admin/medias/images',function($html){
            modal.find('.modal-body').html($html);
        });
    });
    /*======================================
     NAVIGATION AJAX
     ========================================*/
    var chargeUnefois = false;

    function pageTitle(value) {
        var title = $('title').attr('data-titre');
        $('title').html(title + " | " + value);
    }

    function showContent(url, title, element) {
        $.get(url, function (data) {
            $('#page-wrapper').fadeOut(500, function () {
                $('#page-wrapper').html(data);
                pageTitle(title);
                $('ul.nav a').removeClass('selected');
                if (element !== undefined) element.addClass('selected');
                $('#page-wrapper').fadeIn(500);
                chargeUnefois = true;
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast');
            });
        });
    }

    $('body').on('click', '.ajax:not("a.del")', function (e) {
        e.preventDefault();
        var a = $(this);
        var url = a.attr('href');
        var title = a.attr('title');
        showContent(url, title, a);
        history.pushState({
            titre: title
        }, title, url);
        e.stopPropagation();
    });
    window.onpopstate = function (event) {
        if (event.state == null && chargeUnefois) showContent(document.location.pathname, 'Cours accessible à tous'); //window.location.reload();
        else showContent(document.location.pathname, event.state.titre);
    }
});