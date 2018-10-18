/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(1);
__webpack_require__(2);
module.exports = __webpack_require__(3);


/***/ }),
/* 1 */
/***/ (function(module, exports) {


/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

//equire('./bootstrap');
//require('jquery');


$(document).ready(function () {

    // PHOTO PLACE JS
    //---------------
    $('.photo-place.enabled').click(function () {

        var photoName = $(this).data('photo-name');

        if (!$(this).hasClass("photo-place-filled")) {
            $("#" + photoName + "-deleted").val('');
            $("#" + photoName).trigger('click');
        } else {
            $("#" + photoName).val('');
            $(this).find('img').remove();
            $("#" + photoName + "-deleted").val('true');
            $(this).removeClass('photo-place-filled');
            $(this).find('.fa.fa-plus').show();
        }
    });

    $('.input-photo').on('change', function () {

        readURL(this);
    });

    function readURL(input) {

        var photoName = $(input).attr('id');

        if (input.files && input.files[0]) {

            var reader = new FileReader();

            reader.onload = function (e) {

                $(".photo-place[data-photo-name='" + photoName + "'] .photo-holder .fa.fa-plus").hide();
                $(".photo-place[data-photo-name='" + photoName + "']").addClass('photo-place-filled');
                $(".photo-place[data-photo-name='" + photoName + "'] .photo-holder img").remove();
                $(".photo-place[data-photo-name='" + photoName + "'] .photo-holder").append("<img style='vertical-align: middle; display: table-cell' class='photo-place-image' src='" + e.target.result + "'>");
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
    //---------------
    // PHOTO PLACE JS END

    $('[data-toggle="tooltip"]').tooltip();
});

/***/ }),
/* 2 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 3 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })
/******/ ]);