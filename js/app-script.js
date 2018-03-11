$(function(){
    
    var pathname = window.location.pathname;
    
    /// For button file input ///////////////////////////////////////////////////////////

    $('.custom-file-input').on('change',function(){
        var fileName = $(this).val();
        $(this).next('.form-control-file').addClass("selected").html(fileName);
    })
    
    /// For category navigation   ///////////////////////////////////////////////////////
    
    if ($(document).width() > 1200) {
        $('.fixed').css('position', 'fixed');
        $('.app-nav a').css('width', '264px');
    } else if ($(document).width() > 992 && $(document).width() < 1200) {
        $('.fixed').css('position', 'fixed');
        $('.app-nav a').css('width', '218px');
    } else {
        $('.app-nav').css('marginTop', '50px');
        $('.app-nav a').css('display', 'block');
    }
    
    $(".topnav").accordion({
        accordion:true,
        speed: 500,
        closedSign: '<span class="dropdown-toggle"></span>',
        openedSign: '<span class="dropup"><span class="dropdown-toggle"></span></span>'
    });
    
    // In order don't work link which has children

    $('ul.topnav li a').on('click', function(){
        if ($(this).parent('li').has('ul').length != 0) {
            return false;
        }
    });
    
    // To add padding to each nested link in menu
    
    var menuLinks = $('ul.topnav').find('a');

    menuLinks.each(function(){
        var parentsUntilLength = $(this).parentsUntil('.app-nav').length;
        var div = parentsUntilLength / 2;

        if (div > 1) {
            var res = (div - 1) * 40;
            $(this).css('paddingLeft', res + 'px');
        }
    })
 
    /// Setting for class footer ////////////////////////////////////////////////////////
    
    if ($(document).width() < 370) {
        $('.footer').css('lineHeight', '2');
    }
    
    /// Setting for input type file /////////////////////////////////////////////////////
    
    if ($(document).width() > 432 && $(document).width() < 992) {
        $(":file").jfilestyle({
            theme: "custom",
            inputSize: "300px"
        });
    } else if ($(document).width() > 338 && $(document).width() <= 432) {
        $(":file").jfilestyle({
            theme: "custom",
            inputSize: "200px"
        }); 
    } else if ($(document).width() <= 338) {
        $(":file").jfilestyle({
            theme: "custom",
            input: false
        });  
    } else {
        $(":file").jfilestyle({
            theme: "custom",
            inputSize: "400px"
        });
    }
    
    /// Highlighting for top menu ( pathname is defined at the top of the page ) ////////
	
	$('ul.navbar-nav > li > a[href="' + pathname + '"]').parent().addClass('active');
    
    /// For nested menu Admin ///////////////////////////////////////////////////////////
	
	if ( pathname.match(new RegExp("/admin/")) ) {
        $('ul.navbar-nav > li > a[href="/admin"]').parent().addClass('active');
    }
    
    /// Highlighting for aside menu  ////////////////////////////////////////////////////
    
    $('ul.topnav li a').each(function(){
        var href = $(this).attr('href');
        
        if ( (href == pathname) ) {
            $(this).addClass('highlight');
        } else {
            $(this).removeClass('highlight');
        }
    });
    
    /// Confirm plugin for delete buttons ///////////////////////////////////////////////
	
	$('.confirm-plugin').jConfirmAction({
		question: 'Are you sure?',
		noText: 'Cancel'
	});
	
	/// For back to top button //////////////////////////////////////////////////////////
    
    $(window).scroll(function () {
		if ($(this).scrollTop() > 1000) {
			$('#back-to-top').fadeIn();
		} else {
			$('#back-to-top').fadeOut();
		}
	});
	
	// scroll body to 0px on click
	$('#back-to-top').click(function () {					
		$('body,html').animate({
			scrollTop: 0
		}, 500);
		return false;
	});
    
    /// Bootstrap 4 tooltips ////////////////////////////////////////////////////////////
    
    if ($(document).width() >= 992) {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    ///
    
    $('form#add-article-form').on('submit', function(){
        if ($('.ck-editor__editable p').text().length == 0) {
            $('#editor-message').text('Please, add some content');
            return false;
        } else {
            return true;
        }
    });
    
    $('form#edit-article-form').on('submit', function(){
        if ($('.ck-editor__editable p').text().length == 0) {
            $('#editor-message').text('Please, add some content');
            return false;
        } else {
            return true;
        }
    });    
    
    /// For user delete modal ///////////////////////////////////////////////////////////

    $('#delete-admin').click('on', function(){
        $('.modal-form').submit();
    });
    
    /// For navbar search ///////////////////////////////////////////////////////////////
    
    if ($(document).width() < 576) {
        
        $('#navbar-search button').css({
            borderTopLeftRadius: '.2rem',
            borderBottomLeftRadius: '.2rem',
            marginLeft: 0,
            marginTop: '5px',
        });
    }
    
    /// Settings ////////////////////////////////////////////////////////////////////////
    
    if ($(document).width() < 768) {
        $('div.app-breadcrumbs').removeClass('text-right');
    }
    
    if ($(document).width() < 576) {
        $('div.greeting-admin').removeClass('text-right').css('marginBottom', '10px');
    }
    
    if ($(document).width() < 992) {
        $('<hr>').css('background', '#fff').insertBefore('#navbar-search');
        $('#navbar-search').css('margin', '18px 0 10px 0');       
    }
    
    /// Add class 'active' to first slider's element ( without it slider will not work ) //////////
    
    if(pathname == '/') {
        $('.carousel-item').eq(0).addClass('active');
    }
    
    /// For search result /////////////////////////////////////////////////////////////////////////
    
    if(pathname == '/search') {
        var str = $('h5').html();

        if (str.length > 22) {
            var res1 = str.replace(/"/i, '"<span class="mark">');
            var res2 = res1.replace(/"$/i, '</span>"');
            $('h5').html(res2);
        }       
    }
    
    /// Setting for footer social icons ///////////////////////////////////////////////////////////
    
    if ($(document).width() < 576) {
        $('#social-acons-block').css('marginTop', '10px');
    }
    
    /// For portfolio   ///////////////////////////////////////////////////////////////////////////

    if ($(document).width() < 768) {
        $('.portfolio').css('marginBottom', '40px');
    }
    
    /// For comment ///////////////////////////////////////////////////////////////////////////////
    
    var nl2br = function (str) {
        if (str) {
            return str.replace(/([^>])\n/g, '$1<br/>');
        }
    }
    
    var addComment = function() {
        var commentData = $('#comment-form').serialize();

        $.post(
            '/comment/add',
            commentData,
            function(data) {
                
                // 'Page not found' will throw application exception, from Router, if do not find the page
                if (data && data != 'Page not found') {
                    
                    var data = JSON.parse(data);

                    if (! $.isEmptyObject(data.errors)) {
                        $('#csrfError').text(data.errors.csrfError);
                        $('#emailError').text(data.errors.emailError);
                    } else {
                        if (! $.isEmptyObject(data.output)) {                           
                            $('#new-comment h5').text(data.output.user_nickname);

                            var content = nl2br(data.output.content);
                            $('#comment-text').html(content);

                            $('#new-comment span').append("&nbsp;&nbsp;<span>" + data.output.entry_date + "</span>");
                            $('#new-comment').show(400);                          
                            $('#comment-form input[type="submit"]').attr('disabled', 'disabled');
                            
                            $('#comment-form')[0].reset(); // reset form fields
                        }
                    } 
                } 
            }
        );
    }
    
    $('#comment-form').on('submit', function(e){
        e.preventDefault();
        addComment();   
    });

    /// For searching comments in manage comments (admin area) ////////////////////////////////////
    
    $("#myInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#myTable tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    /// For load more process /////////////////////////////////////////////////////////////////////
    
    var cardCountToLoad = 6;
    
    if ($(".card-content:hidden").length == 0 || $(".card-content:hidden").length <= cardCountToLoad) {
        $("#loadMore").hide();
    }
    
    $(".card-content").slice(0, cardCountToLoad).show();
    
    $("#loadMore").on('click', function (e) {
        e.preventDefault();
        $(".card-content:hidden").slice(0, cardCountToLoad).slideDown();
        if ($(".card-content:hidden").length == 0) {
            $("#loadMore").fadeOut('slow');
        }
        $('html,body').animate({
            //scrollTop: $(this).offset().top // for scrolling to 'load more' button after click
        }, 1500);
    });
    
    /// For raiting process ///////////////////////////////////////////////////////////////////////
    
    if ( pathname.match(new RegExp("/page/[0-9]+")) ) {
        
        var dataAVG = $('form#ratingsForm').attr('data-avg');
        
        for (var i = 0; i < dataAVG; i++) {
            $('form#ratingsForm label i').eq(i).css('color', '#369').css('fontWeight', 'bold');
        }      

        var starsCount = $('form#ratingsForm label i').length;

        for (var j = 0; j < starsCount; j++) {

            $( "form#ratingsForm label i" ).eq(j)
                .mouseenter(function() {
                    $(this).css('color', '#d00');
                })
                .mouseleave(function() {
                    $(this).css('color', '#369');
                });
        }       
        
        /* This function shows messages in a pretty way */
        var ratingMessage = function (message) {
            $('#already-rated').html(message).fadeIn(300);
            
            setTimeout(function() {
                $('#already-rated').html(message).fadeOut(300);
            }, 3000);
        };          
        
        var ratingProcess = function(ratingStars) {
            var articleId = $('form#ratingsForm').attr('data-article-id');
            
            dataObj = {
                ratingStars: ratingStars,
                articleId: articleId
            }
            
            $.post(
                '/raiting/add',
                dataObj,
                function(data){
                    
                    var data = JSON.parse(data);
                    
                    if (! $.isEmptyObject(data.output)) {  
                        var dataAVG = data.output.avgRaiting;

                        for (var i = 0; i < dataAVG; i++) {
                            $('form#ratingsForm label i').eq(i).css('color', '#369').css('fontWeight', 'bold');
                        }
                        
                        $('#raitings-count').text(data.output.raitingsCount);
                        
                        if(typeof(data.output.userRaitingCount) != "undefined" && data.output.userRaitingCount !== null) {
                            ratingMessage('You already rated <strong>' + data.output.userRaitingCount + '</strong> star(s)');
                        }
                    }
                }
            ); 
        }
    
        var manageStars = function ($this) {
            $('form#ratingsForm label i').css('color', '#369').css('fontWeight', 'normal');

            var ratingValue = $this.parent().children('input[name="star"]').prop("checked", true).val();

            /* 
                This block for showing stars you selected.
                After showing average results we do not need this block anymore.
            */
            /*
            if (ratingValue != 0) {
                for (var i = 0; i < ratingValue; i++) {
                    $('form#ratingsForm label i').eq(i).css('color', '#369').css('fontWeight', 'bold');
                } 
            }
            */            

            return ratingValue;
        }

        $('form#ratingsForm label i').on('click', function(e){
            e.preventDefault();

            var ratingStars = manageStars($(this));
            
            ratingProcess(ratingStars);
            
        });
        
    }
    
    /// End raiting process block /////////////////////////////////////////////////////////////////
    
});
