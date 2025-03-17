/*Toggle*/
$(function () {
    $(".toggle-content").click(function () {
        $("#menunew").slideToggle();
    });

    $(".toggle-btn button").click(function () {
        $(".mainMenu .menu").slideToggle();
    });
    $(".db-s-menu li.hasChild a").click(function (e) {
        e.preventDefault();
        $(this).parents("li").toggleClass("active");
        $(this).parents("li").find("ul").slideToggle();
    });
    $(".db-menu-toggle-btn").click(function () {
        $("#db-menu-toggle").fadeToggle(function () {
            $("#db-menu-toggle").toggleClass("active");
        });
    });

    $("form").on("change", ".file-upload-field", function () {
        $(this).parent(".file-upload-wrapper").attr("data-text", $(this).val().replace(/.*(\/|\\)/, ''));
    });

    $('#chooseFile').bind('change', function () {
        var filename = $("#chooseFile").val();
        if (/^\s*$/.test(filename)) {
            $(".file-upload").removeClass('active');
            $("#noFile").text("No file chosen...");
        } else {
            $(".file-upload").addClass('active');
            $("#noFile").text(filename.replace("C:\\fakepath\\", ""));
        }
    });

    $(".db-form-toggle .all-forms > ul > li > a").click(function (e) {
        e.preventDefault();
        $(".db-form-toggle .all-forms > ul > li").not($(this).parents("li")).removeClass("active");
        $(this).parents("li").toggleClass("active");

        $(".db-form-toggle .all-forms > ul > li .toggle-form-cont").not($(this).parents("li").find(".toggle-form-cont")).slideUp();
        $(this).parents("li").find(".toggle-form-cont").slideToggle();
    });
});
