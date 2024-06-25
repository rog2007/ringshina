/**
 * jQuery lightBox plugin
 * This jQuery plugin was inspired and based on Lightbox 2 by Lokesh Dhakar (http://www.huddletogether.com/projects/lightbox2/)
 * and adapted to me for use like a plugin from jQuery.
 * @name jquery-lightbox-0.5.js
 * @author Leandro Vieira Pinho - http://leandrovieira.com
 * @version 0.5
 * @date April 11, 2008
 * @category jQuery plugin
 * @copyright (c) 2008 Leandro Vieira Pinho (leandrovieira.com)
 * @license CCAttribution-ShareAlike 2.5 Brazil - http://creativecommons.org/licenses/by-sa/2.5/br/deed.en_US
 * @example Visit http://leandrovieira.com/projects/jquery/lightbox/ for more informations about this jQuery plugin
 */

// Offering a Custom Alias suport - More info: http://docs.jquery.com/Plugins/Authoring#Custom_Alias
(function ($) {
    /**
     * $ is an alias to jQuery object
     *
     */
    $.fn.lightBox = function (settings) {
        // Settings to configure the jQuery lightBox plugin how you like
        settings = jQuery.extend({
            // Configuration related to overlay
            overlayBgColor: '#000', // (string) Background color to overlay; inform a hexadecimal value like: #RRGGBB. Where RR, GG, and BB are the hexadecimal values for the red, green, and blue values of the color.
            overlayOpacity: 0.8, // (integer) Opacity value to overlay; inform: 0.X. Where X are number from 0 to 9
            // Configuration related to navigation
            fixedNavigation: false, // (boolean) Boolean that informs if the navigation (next and prev button) will be fixed or not in the interface.
            // Configuration related to images
            imageLoading: 'images/lightbox-ico-loading.gif', // (string) Path and the name of the loading icon
            imageBtnPrev: 'images/lightbox-btn-prev.gif', // (string) Path and the name of the prev button image
            imageBtnNext: 'images/lightbox-btn-next.gif', // (string) Path and the name of the next button image
            imageBtnClose: 'images/lightbox-btn-close.gif', // (string) Path and the name of the close btn
            imageBlank: 'images/lightbox-blank.gif', // (string) Path and the name of a blank image (one pixel)
            // Configuration related to container image box
            containerBorderSize: 10, // (integer) If you adjust the padding in the CSS for the container, #lightbox-container-image-box, you will need to update this value
            containerResizeSpeed: 400, // (integer) Specify the resize duration of container image. These number are miliseconds. 400 is default.
            // Configuration related to texts in caption. For example: Image 2 of 8. You can alter either "Image" and "of" texts.
            txtImage: 'Image', // (string) Specify text "Image"
            txtOf: 'of', // (string) Specify text "of"
            // Configuration related to keyboard navigation
            keyToClose: 'c', // (string) (c = close) Letter to close the jQuery lightBox interface. Beyond this letter, the letter X and the SCAPE key is used to.
            keyToPrev: 'p', // (string) (p = previous) Letter to show the previous image
            keyToNext: 'n', // (string) (n = next) Letter to show the next image.
            // Don´t alter these variables in any way
            imageArray: [],
            activeImage: 0
        }, settings);
        // Caching the jQuery object with all elements matched
        var jQueryMatchedObj = this; // This, in this context, refer to jQuery object
        /**
         * Initializing the plugin calling the start function
         *
         * @return boolean false
         */
        function _initialize() {
            _start(this, jQueryMatchedObj); // This, in this context, refer to object (link) which the user have clicked
            return false; // Avoid the browser following the link
        }
        /**
         * Start the jQuery lightBox plugin
         *
         * @param object objClicked The object (link) whick the user have clicked
         * @param object jQueryMatchedObj The jQuery object with all elements matched
         */
        function _start(objClicked, jQueryMatchedObj) {
            // Hime some elements to avoid conflict with overlay in IE. These elements appear above the overlay.
            $('embed, object, select').css({'visibility': 'hidden'});
            // Call the function to create the markup structure; style some elements; assign events in some elements.
            _set_interface();
            // Unset total images in imageArray
            settings.imageArray.length = 0;
            // Unset image active information
            settings.activeImage = 0;
            // We have an image set? Or just an image? Let´s see it.
            if (jQueryMatchedObj.length == 1) {
                settings.imageArray.push(new Array(objClicked.getAttribute('href'), objClicked.getAttribute('title')));
            } else {
                // Add an Array (as many as we have), with href and title atributes, inside the Array that storage the images references		
                for (var i = 0; i < jQueryMatchedObj.length; i++) {
                    settings.imageArray.push(new Array(jQueryMatchedObj[i].getAttribute('href'), jQueryMatchedObj[i].getAttribute('title')));
                }
            }
            while (settings.imageArray[settings.activeImage][0] != objClicked.getAttribute('href')) {
                settings.activeImage++;
            }
            // Call the function that prepares image exibition
            _set_image_to_view();
        }
        /**
         * Create the jQuery lightBox plugin interface
         *
         * The HTML markup will be like that:
         <div id="jquery-overlay"></div>
         <div id="jquery-lightbox">
         <div id="lightbox-container-image-box">
         <div id="lightbox-container-image">
         <img src="../fotos/XX.jpg" id="lightbox-image">
         <div id="lightbox-nav">
         <a href="#" id="lightbox-nav-btnPrev"></a>
         <a href="#" id="lightbox-nav-btnNext"></a>
         </div>
         <div id="lightbox-loading">
         <a href="#" id="lightbox-loading-link">
         <img src="../images/lightbox-ico-loading.gif">
         </a>
         </div>
         </div>
         </div>
         <div id="lightbox-container-image-data-box">
         <div id="lightbox-container-image-data">
         <div id="lightbox-image-details">
         <span id="lightbox-image-details-caption"></span>
         <span id="lightbox-image-details-currentNumber"></span>
         </div>
         <div id="lightbox-secNav">
         <a href="#" id="lightbox-secNav-btnClose">
         <img src="../images/lightbox-btn-close.gif">
         </a>
         </div>
         </div>
         </div>
         </div>
         *
         */
        function _set_interface() {
            // Apply the HTML markup into body tag
            $('body').append('<div id="jquery-overlay"></div><div id="jquery-lightbox"><div id="lightbox-container-image-box"><div id="lightbox-container-image"><img id="lightbox-image"><div style="" id="lightbox-nav"><a href="#" id="lightbox-nav-btnPrev"></a><a href="#" id="lightbox-nav-btnNext"></a></div><div id="lightbox-loading"><a href="#" id="lightbox-loading-link"><img src="/adm/' + settings.imageLoading + '"></a></div></div></div><div id="lightbox-container-image-data-box"><div id="lightbox-container-image-data"><div id="lightbox-image-details"><span id="lightbox-image-details-caption"></span><span id="lightbox-image-details-currentNumber"></span></div><div id="lightbox-secNav"><a href="#" id="lightbox-secNav-btnClose"><img src="/adm/' + settings.imageBtnClose + '"></a></div></div></div></div>');
            // Get page sizes
            var arrPageSizes = ___getPageSize();
            // Style overlay and show it
            $('#jquery-overlay').css({
                backgroundColor: settings.overlayBgColor,
                opacity: settings.overlayOpacity,
                width: arrPageSizes[0],
                height: arrPageSizes[1]
            }).fadeIn();
            // Get page scroll
            var arrPageScroll = ___getPageScroll();
            // Calculate top and left offset for the jquery-lightbox div object and show it
            $('#jquery-lightbox').css({
                top: arrPageScroll[1] + (arrPageSizes[3] / 10),
                left: arrPageScroll[0]
            }).show();
            // Assigning click events in elements to close overlay
            $('#jquery-overlay,#jquery-lightbox').click(function () {
                _finish();
            });
            // Assign the _finish function to lightbox-loading-link and lightbox-secNav-btnClose objects
            $('#lightbox-loading-link,#lightbox-secNav-btnClose').click(function () {
                _finish();
                return false;
            });
            // If window was resized, calculate the new overlay dimensions
            $(window).resize(function () {
                // Get page sizes
                var arrPageSizes = ___getPageSize();
                // Style overlay and show it
                $('#jquery-overlay').css({
                    width: arrPageSizes[0],
                    height: arrPageSizes[1]
                });
                // Get page scroll
                var arrPageScroll = ___getPageScroll();
                // Calculate top and left offset for the jquery-lightbox div object and show it
                $('#jquery-lightbox').css({
                    top: arrPageScroll[1] + (arrPageSizes[3] / 10),
                    left: arrPageScroll[0]
                });
            });
        }
        /**
         * Prepares image exibition; doing a image´s preloader to calculate it´s size
         *
         */
        function _set_image_to_view() { // show the loading
            // Show the loading
            $('#lightbox-loading').show();
            if (settings.fixedNavigation) {
                $('#lightbox-image,#lightbox-container-image-data-box,#lightbox-image-details-currentNumber').hide();
            } else {
                // Hide some elements
                $('#lightbox-image,#lightbox-nav,#lightbox-nav-btnPrev,#lightbox-nav-btnNext,#lightbox-container-image-data-box,#lightbox-image-details-currentNumber').hide();
            }
            // Image preload process
            var objImagePreloader = new Image();
            objImagePreloader.onload = function () {
                $('#lightbox-image').attr('src', settings.imageArray[settings.activeImage][0]);
                // Perfomance an effect in the image container resizing it
                _resize_container_image_box(objImagePreloader.width, objImagePreloader.height);
                //	clear onLoad, IE behaves irratically with animated gifs otherwise
                objImagePreloader.onload = function () {};
            };
            objImagePreloader.src = settings.imageArray[settings.activeImage][0];
        }
        ;
        /**
         * Perfomance an effect in the image container resizing it
         *
         * @param integer intImageWidth The image´s width that will be showed
         * @param integer intImageHeight The image´s height that will be showed
         */
        function _resize_container_image_box(intImageWidth, intImageHeight) {
            // Get current width and height
            var intCurrentWidth = $('#lightbox-container-image-box').width();
            var intCurrentHeight = $('#lightbox-container-image-box').height();
            // Get the width and height of the selected image plus the padding
            var intWidth = (intImageWidth + (settings.containerBorderSize * 2)); // Plus the image´s width and the left and right padding value
            var intHeight = (intImageHeight + (settings.containerBorderSize * 2)); // Plus the image´s height and the left and right padding value
            // Diferences
            var intDiffW = intCurrentWidth - intWidth;
            var intDiffH = intCurrentHeight - intHeight;
            // Perfomance the effect
            $('#lightbox-container-image-box').animate({width: intWidth, height: intHeight}, settings.containerResizeSpeed, function () {
                _show_image();
            });
            if ((intDiffW == 0) && (intDiffH == 0)) {
                if ($.browser.msie) {
                    ___pause(250);
                } else {
                    ___pause(100);
                }
            }


            $('#lightbox-container-image-data-box').css({width: intImageWidth});
            $('#lightbox-nav-btnPrev,#lightbox-nav-btnNext').css({height: intImageHeight + (settings.containerBorderSize * 2)});
        }
        ;
        /**
         * Show the prepared image
         *
         */
        function _show_image() {
            $('#lightbox-loading').hide();
            $('#lightbox-image').fadeIn(function () {
                _show_image_data();
                _set_navigation();
            });
            _preload_neighbor_images();
        }
        ;
        /**
         * Show the image information
         *
         */
        function _show_image_data() {
            $('#lightbox-container-image-data-box').slideDown('fast');
            $('#lightbox-image-details-caption').hide();
            if (settings.imageArray[settings.activeImage][1]) {
                $('#lightbox-image-details-caption').html(settings.imageArray[settings.activeImage][1]).show();
            }
            // If we have a image set, display 'Image X of X'
            if (settings.imageArray.length > 1) {
                $('#lightbox-image-details-currentNumber').html(settings.txtImage + ' ' + (settings.activeImage + 1) + ' ' + settings.txtOf + ' ' + settings.imageArray.length).show();
            }
        }
        /**
         * Display the button navigations
         *
         */
        function _set_navigation() {
            $('#lightbox-nav').show();

            // Instead to define this configuration in CSS file, we define here. And it´s need to IE. Just.
            $('#lightbox-nav-btnPrev,#lightbox-nav-btnNext').css({'background': 'transparent url(/adm/' + settings.imageBlank + ') no-repeat'});

            // Show the prev button, if not the first image in set
            if (settings.activeImage != 0) {
                if (settings.fixedNavigation) {
                    $('#lightbox-nav-btnPrev').css({'background': 'url(/adm/' + settings.imageBtnPrev + ') left 15% no-repeat'})
                            .unbind()
                            .bind('click', function () {
                                settings.activeImage = settings.activeImage - 1;
                                _set_image_to_view();
                                return false;
                            });
                } else {
                    // Show the images button for Next buttons
                    $('#lightbox-nav-btnPrev').unbind().hover(function () {
                        $(this).css({'background': 'url(/adm/' + settings.imageBtnPrev + ') left 15% no-repeat'});
                    }, function () {
                        $(this).css({'background': 'transparent url(/adm/' + settings.imageBlank + ') no-repeat'});
                    }).show().bind('click', function () {
                        settings.activeImage = settings.activeImage - 1;
                        _set_image_to_view();
                        return false;
                    });
                }
            }

            // Show the next button, if not the last image in set
            if (settings.activeImage != (settings.imageArray.length - 1)) {
                if (settings.fixedNavigation) {
                    $('#lightbox-nav-btnNext').css({'background': 'url(/adm/' + settings.imageBtnNext + ') right 15% no-repeat'})
                            .unbind()
                            .bind('click', function () {
                                settings.activeImage = settings.activeImage + 1;
                                _set_image_to_view();
                                return false;
                            });
                } else {
                    // Show the images button for Next buttons
                    $('#lightbox-nav-btnNext').unbind().hover(function () {
                        $(this).css({'background': 'url(/adm/' + settings.imageBtnNext + ') right 15% no-repeat'});
                    }, function () {
                        $(this).css({'background': 'transparent url(/adm/' + settings.imageBlank + ') no-repeat'});
                    }).show().bind('click', function () {
                        settings.activeImage = settings.activeImage + 1;
                        _set_image_to_view();
                        return false;
                    });
                }
            }
            // Enable keyboard navigation
            _enable_keyboard_navigation();
        }
        /**
         * Enable a support to keyboard navigation
         *
         */
        function _enable_keyboard_navigation() {
            $(document).keydown(function (objEvent) {
                _keyboard_action(objEvent);
            });
        }
        /**
         * Disable the support to keyboard navigation
         *
         */
        function _disable_keyboard_navigation() {
            $(document).unbind();
        }
        /**
         * Perform the keyboard actions
         *
         */
        function _keyboard_action(objEvent) {
            // To ie
            if (objEvent == null) {
                keycode = event.keyCode;
                escapeKey = 27;
                // To Mozilla
            } else {
                keycode = objEvent.keyCode;
                escapeKey = objEvent.DOM_VK_ESCAPE;
            }
            // Get the key in lower case form
            key = String.fromCharCode(keycode).toLowerCase();
            // Verify the keys to close the ligthBox
            if ((key == settings.keyToClose) || (key == 'x') || (keycode == escapeKey)) {
                _finish();
            }
            // Verify the key to show the previous image
            if ((key == settings.keyToPrev) || (keycode == 37)) {
                // If we´re not showing the first image, call the previous
                if (settings.activeImage != 0) {
                    settings.activeImage = settings.activeImage - 1;
                    _set_image_to_view();
                    _disable_keyboard_navigation();
                }
            }
            // Verify the key to show the next image
            if ((key == settings.keyToNext) || (keycode == 39)) {
                // If we´re not showing the last image, call the next
                if (settings.activeImage != (settings.imageArray.length - 1)) {
                    settings.activeImage = settings.activeImage + 1;
                    _set_image_to_view();
                    _disable_keyboard_navigation();
                }
            }
        }
        /**
         * Preload prev and next images being showed
         *
         */
        function _preload_neighbor_images() {
            if ((settings.imageArray.length - 1) > settings.activeImage) {
                objNext = new Image();
                objNext.src = settings.imageArray[settings.activeImage + 1][0];
            }
            if (settings.activeImage > 0) {
                objPrev = new Image();
                objPrev.src = settings.imageArray[settings.activeImage - 1][0];
            }
        }
        /**
         * Remove jQuery lightBox plugin HTML markup
         *
         */
        function _finish() {
            $('#jquery-lightbox').remove();
            $('#jquery-overlay').fadeOut(function () {
                $('#jquery-overlay').remove();
            });
            // Show some elements to avoid conflict with overlay in IE. These elements appear above the overlay.
            $('embed, object, select').css({'visibility': 'visible'});
        }
        /**
         / THIRD FUNCTION
         * getPageSize() by quirksmode.com
         *
         * @return Array Return an array with page width, height and window width, height
         */
        function ___getPageSize() {
            var xScroll, yScroll;
            if (window.innerHeight && window.scrollMaxY) {
                xScroll = window.innerWidth + window.scrollMaxX;
                yScroll = window.innerHeight + window.scrollMaxY;
            } else if (document.body.scrollHeight > document.body.offsetHeight) { // all but Explorer Mac
                xScroll = document.body.scrollWidth;
                yScroll = document.body.scrollHeight;
            } else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
                xScroll = document.body.offsetWidth;
                yScroll = document.body.offsetHeight;
            }
            var windowWidth, windowHeight;
            if (self.innerHeight) {	// all except Explorer
                if (document.documentElement.clientWidth) {
                    windowWidth = document.documentElement.clientWidth;
                } else {
                    windowWidth = self.innerWidth;
                }
                windowHeight = self.innerHeight;
            } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
                windowWidth = document.documentElement.clientWidth;
                windowHeight = document.documentElement.clientHeight;
            } else if (document.body) { // other Explorers
                windowWidth = document.body.clientWidth;
                windowHeight = document.body.clientHeight;
            }
            // for small pages with total height less then height of the viewport
            if (yScroll < windowHeight) {
                pageHeight = windowHeight;
            } else {
                pageHeight = yScroll;
            }
            // for small pages with total width less then width of the viewport
            if (xScroll < windowWidth) {
                pageWidth = xScroll;
            } else {
                pageWidth = windowWidth;
            }
            arrayPageSize = new Array(pageWidth, pageHeight, windowWidth, windowHeight);
            return arrayPageSize;
        }
        ;
        /**
         / THIRD FUNCTION
         * getPageScroll() by quirksmode.com
         *
         * @return Array Return an array with x,y page scroll values.
         */
        function ___getPageScroll() {
            var xScroll, yScroll;
            if (self.pageYOffset) {
                yScroll = self.pageYOffset;
                xScroll = self.pageXOffset;
            } else if (document.documentElement && document.documentElement.scrollTop) {	 // Explorer 6 Strict
                yScroll = document.documentElement.scrollTop;
                xScroll = document.documentElement.scrollLeft;
            } else if (document.body) {// all other Explorers
                yScroll = document.body.scrollTop;
                xScroll = document.body.scrollLeft;
            }
            arrayPageScroll = new Array(xScroll, yScroll);
            return arrayPageScroll;
        }
        ;
        /**
         * Stop the code execution from a escified time in milisecond
         *
         */
        function ___pause(ms) {
            var date = new Date();
            curDate = null;
            do {
                var curDate = new Date();
            } while (curDate - date < ms);
        }
        ;
        // Return the jQuery object for chaining. The unbind method is used to avoid click conflict when the plugin is called more than once
        return this.unbind('click').click(_initialize);
    };
})(jQuery); // Call and execute the function immediately passing the jQuery object




function admin_add_ajax(st){
    let frm = document.add;
    add_options_ajax(st, frm);
}

function filter_add(st){
    let frm = document.filters;
    add_options_ajax(st, frm);
}

function add_options_ajax(st, frm){
    let urlBody = "?";
    let xmlhr = new XMLHttpRequest();
    if(st == 1){
        urlBody += "vend=" + encodeURIComponent(frm.vend.options[frm.vend.selectedIndex].value);
        frm = frm.model;
    }else if(st == 2){
        urlBody += "model=" + encodeURIComponent(frm.model.options[frm.model.selectedIndex].value);
        frm = frm.year;
    }else if(st == 3){
        urlBody += "year=" + encodeURIComponent(frm.year.options[frm.year.selectedIndex].value);
        frm = frm.modif;
    }

    xmlhr.open(
        method="GET",
        url="/adm/get_options.php" + urlBody,
        async=true
    );

    xmlhr.onload = function(){
        let data = JSON.parse(xmlhr.response);
        frm.innerHTML = '';
        let ch = document.createElement("option");
        ch.value = "all";
        ch.innerHTML = "все";
        frm.appendChild(ch);
        frm.enabled = "enabled";
        frm.disabled = false;
        for(let i = 0;i < data.length;i++){
            let ch = document.createElement("option");
            ch.value = data[i]["id"];
            ch.innerHTML = data[i]["name"];
            frm.appendChild(ch);
        }
    }

    xmlhr.send();
}

function delete_for_id(table, id){
    if(confirm("Вы точно хотите удалить эту запись и всех её детей?")){
        let xmlhr = new XMLHttpRequest();
        let url = "/adm/podbor_save/" + table + "/" + id + "/";
        xmlhr.open(
            method="POST",
            url=url,
            async=true
        );

        xmlhr.onload = function(){
            rows_list = document.getElementsByClassName("skld");
            for(let i = 1;i < rows_list.length;i++){
                if(rows_list[i].getElementsByClassName("identificator")[0].innerHTML == id){
                    rows_list[i].remove();
                    break;
                }
            }
        }

        let form = new FormData();
        form.append("mode", "delete");
        form.append("is_ajax", true);
        xmlhr.send(form);
    }
}

function set_selected(id, value){
    let elem = document.getElementById(id);
    elem.value = value;
}

function get_td(value, name){
    let td = document.createElement("td");
    let input = document.createElement("input");
    input.value = value;
    input.className = name;
    input.style = "width:50px;margin:0px;";
    td.appendChild(input);
    td.style = "width:50px;";
    return td;
}

function get_wheel_edit(wheelInfo){
    let tr = document.createElement("tr");
    tr.appendChild(get_td(wheelInfo["is_stock"], "is_stock"));
    tr.appendChild(get_td(wheelInfo["showing_fp_only"], "showing_fp_only"));

    tr.appendChild(get_td(wheelInfo["front"]["rim_diameter"], "front.rim_diameter"));
    tr.appendChild(get_td(wheelInfo["front"]["rim_width"], "front.rim_width"));
    tr.appendChild(get_td(wheelInfo["front"]["rim_offset"], "front.rim_offset"));
    tr.appendChild(get_td(wheelInfo["front"]["tire_construction"], "front.tire_construction"));
    tr.appendChild(get_td(wheelInfo["front"]["tire_width"], "front.tire_width"));
    tr.appendChild(get_td(wheelInfo["front"]["tire_aspect_ratio"], "front.tire_aspect_ratio"));

    tr.appendChild(get_td(wheelInfo["rear"]["rim_diameter"], "rear.rim_diameter"));
    tr.appendChild(get_td(wheelInfo["rear"]["rim_width"], "rear.rim_width"));
    tr.appendChild(get_td(wheelInfo["rear"]["rim_offset"], "rear.rim_offset"));
    tr.appendChild(get_td(wheelInfo["rear"]["tire_construction"], "rear.tire_construction"));
    tr.appendChild(get_td(wheelInfo["rear"]["tire_width"], "rear.tire_width"));
    tr.appendChild(get_td(wheelInfo["rear"]["tire_aspect_ratio"], "rear.tire_aspect_ratio"));
    return tr;
}

function format_data(s){
    if(!isNaN(parseFloat(s)) && isFinite(s)){
        return Number(s);
    }else if(s == "true"){
        return true;
    }else if(s == "false"){
        return false;
    }
    return s;
}

function send_update(id, data){
    let xmlhr = new XMLHttpRequest();
    let url = "/adm/podbor_save/wheelsInfo/" + id + "/";
    xmlhr.open(
        method="POST",
        url=url,
        async=true
    );

    xmlhr.onload = function(){
        alert("обновлено " + id);
    }

    let formData = new FormData();
    formData.append("mode", "update");
    formData.append("is_ajax", true);
    formData.append("data", data);
    xmlhr.send(formData);
}

function edit_submit(){
    let id = document.getElementById("id_for_edit").value;
    let data = JSON.parse(JSON.parse(document.getElementById("data").value)[4]["data"]);

    let info_keys = ["technical.wheel_fasteners.type", "technical.wheel_fasteners.thread_size", "technical.stud_holes", "technical.pcd", "technical.centre_bore"];

    let info_values =[];
    for(let i = 0;i < info_keys.length;i++){
        info_values.push(document.getElementsByClassName(info_keys[i]));
    }

    let wheels = ["is_stock", "showing_fp_only", "front-rim_diameter", "front-rim_width", "front-rim_offset", "front-tire_construction", "front-tire_width", "front-tire_aspect_ratio",
    "rear-rim_diameter", "rear-rim_width", "rear-rim_offset", "rear-tire_construction", "rear-tire_width", "rear-tire_aspect_ratio"];
    let values = [];
    for(let i = 0;i < wheels.length;i++){
        values.push(document.getElementsByClassName(wheels[i]));
    }

    for(let i = 0;i < info_keys.length;i++){
        let path = info_keys[i].split(".");
        if(path.length == 1){
            data[path[0]] = format_data(info_values[i][0].value);
        }else if(path.length == 2){
            data[path[0]][path[1]] = format_data(info_values[i][0].value);
        }else if(path.length == 3){
            data[path[0]][path[1]][path[2]] = format_data(info_values[i][0].value);
        }
    }

    for(let i = 0;i < wheels.length;i++){
        for(let j = 0;j < values[i].length;j++){
            let path = wheels[i].split(".");
            if(path.length == 1){
                data["wheels"][j][path[0]] = format_data(values[i][j].value);
            }else if(path.length == 2){
                data["wheels"][j][path[0]][path[1]] = format_data(values[i][j].value);
            }else if(path.length == 3){
                data["wheels"][j][path[0]][path[1]][path[2]] = format_data(values[i][j].value);
            }
        }
    }

    send_update(id, JSON.stringify(data));
}

function edit_info(id){
    let frm = document.getElementById("edit");
    frm.innerHTML = '';
    let xmlhr = new XMLHttpRequest();
    let url = "/adm/get_info.php?id=" + id;
    xmlhr.open(
        method="GET",
        url=url,
        async=true
    );

    xmlhr.onload = function(){
        let data = JSON.parse(xmlhr.response);
        wheelsInfo = JSON.parse(data[4]["data"]);
        let hidden = document.createElement("input");
        hidden.value = id;
        hidden.type = "hidden";
        hidden.id = "id_for_edit";
        frm.appendChild(hidden);
        hidden = document.createElement("input");
        hidden.value = xmlhr.response;
        hidden.type = "hidden";
        hidden.id = "data";
        frm.appendChild(hidden);
        for(let i = 0;i < data.length - 1;i++){
            let label = document.createElement("p");
            label.innerHTML = data[i]["name"];
            frm.appendChild(label);
        }
        let input = document.createElement("input");
        input.value = wheelsInfo["technical"]["wheel_fasteners"]["type"];
        input.className = "technical.wheel_fasteners.type";
        frm.appendChild(input);
        input = document.createElement("input");
        input.value = wheelsInfo["technical"]["wheel_fasteners"]["thread_size"];
        input.className = "technical.wheel_fasteners.thread_size";
        frm.appendChild(input);
        input = document.createElement("input");
        input.value = wheelsInfo["technical"]["stud_holes"];
        input.className = "technical.stud_holes";
        frm.appendChild(input);
        input = document.createElement("input");
        input.value = wheelsInfo["technical"]["pcd"];
        input.className = "technical.pcd";
        frm.appendChild(input);
        input = document.createElement("input");
        input.value = wheelsInfo["technical"]["centre_bore"];
        input.className = "technical.centre_bore";
        frm.appendChild(input);
        let label = document.createElement("p");
        label.innerHTML = "Колёса";
        frm.appendChild(label);
        console.log(wheelsInfo["wheels"]);
        let table = document.createElement("table");
        let tr = document.createElement("tr");
        tr.appendChild(document.createElement("label"));
        table.appendChild(document.createElement("tr"));
        for(let i = 0;i < wheelsInfo["wheels"].length;i++){
            table.appendChild(get_wheel_edit(wheelsInfo["wheels"][i]));
        }
        frm.appendChild(table);
        let btn = document.createElement("button");
        btn.addEventListener("click", edit_submit);
        btn.innerHTML = "Сохранить";
        frm.appendChild(btn);
    }

    xmlhr.send();
}

function add_wheels_config(){
    let wheels = document.getElementById("wheels_table").getElementsByTagName("tbody")[0];
    let tr = document.createElement("tr");
    let cnt = document.getElementById("count");
    tr.id = "wheel" + cnt.value;
    tr.innerHTML = '<td style="width:10px;"><select name="is_stock' + cnt.value + '"><option value="true">да</option><option value="false" selected="">нет</option></select></td><td style="width:10px;"><select name="showing_fp_only' + cnt.value + '"><option value="true">да</option><option value="false" selected="">нет</option></select></td><td style="width:10px;"><input style="width:130px;" name="front-rim_diameter' + cnt.value + '" value=""></td><td style="width:10px;"><input style="width:130px;" name="front-rim_width' + cnt.value + '" value=""></td><td style="width:10px;"><input style="width:130px;" name="front-rim_offset' + cnt.value + '" value=""></td><input type="hidden" style="width:130px;" name="front-tire_construction' + cnt.value + '" value="R"><td style="width:10px;"><input style="width:130px;" name="front-tire_width' + cnt.value + '" value=""></td><td style="width:10px;"><input style="width:130px;" name="front-tire_aspect_ratio' + cnt.value + '" value=""></td><td style="width:10px;"><input style="width:130px;" name="rear-rim_diameter' + cnt.value + '" value=""></td><td style="width:10px;"><input style="width:130px;" name="rear-rim_width' + cnt.value + '" value=""></td><td style="width:10px;"><input style="width:130px;" name="rear-rim_offset' + cnt.value + '" value=""></td><input type="hidden" style="width:130px;" name="rear-tire_construction' + cnt.value + '" value="R"><td style="width:10px;"><input style="width:130px;" name="rear-tire_width' + cnt.value + '" value=""></td><td style="width:10px;"><input style="width:130px;" name="rear-tire_aspect_ratio' + cnt.value + '" value=""></td><td><button type="button" onclick="del_elem_for_id(&quot;wheel' + cnt.value + '&quot;)">удалить</button></td>';
    wheels.appendChild(tr);

    cnt.value = Number(cnt.value) + 1;
}

function update_wheels_names(){
    let wheels = document.getElementById("wheels_table");
    let children = wheels.children[0].children;
    for(let i = 1;i < children.length;i++){
        children[i].id = "wheel" + (i - 1);
        children[i].children[children[i].children.length - 1].children[0].setAttribute("onclick", "del_elem_for_id('wheel" + (i - 1) + "')");
        let keys = ["is_stock", "showing_fp_only", "front-rim_diameter", "front-rim_width", "front-rim_offset", "front-tire_construction", "front-tire_width", "front-tire_aspect_ratio",
        "rear-rim_diameter", "rear-rim_width", "rear-rim_offset", "rear-tire_construction", "rear-tire_width", "rear-tire_aspect_ratio"];
        for(let j = 0;j < keys.length;j++){
            if(children[i].children[j].tagName == "TD"){
                children[i].children[j].children[0].name = keys[j] + (i - 1);
            }else{
                children[i].children[j].name = keys[j] + (i - 1);
            }
        }
    }
}

function del_elem_for_id(id){
    let elem = document.getElementById(id);
    elem.remove();
    let cnt = document.getElementById("count");
    cnt.value = Number(cnt.value) - 1;
    update_wheels_names();
}