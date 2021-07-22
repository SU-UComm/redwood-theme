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
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
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
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./redwood/assets/js/admin.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./redwood/assets/js/admin.js":
/*!************************************!*\
  !*** ./redwood/assets/js/admin.js ***!
  \************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _admin_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./admin/core */ "./redwood/assets/js/admin/core.js");
/* harmony import */ var _admin_core__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_admin_core__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _admin_customizer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./admin/customizer */ "./redwood/assets/js/admin/customizer.js");
/* harmony import */ var _admin_customizer__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_admin_customizer__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _admin_info_box_widget__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./admin/info_box_widget */ "./redwood/assets/js/admin/info_box_widget.js");
/* harmony import */ var _admin_info_box_widget__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_admin_info_box_widget__WEBPACK_IMPORTED_MODULE_2__);




/***/ }),

/***/ "./redwood/assets/js/admin/core.js":
/*!*****************************************!*\
  !*** ./redwood/assets/js/admin/core.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// if NodeList doesn't support forEach, use Array's forEach()
NodeList.prototype.forEach = NodeList.prototype.forEach || Array.prototype.forEach;

/***/ }),

/***/ "./redwood/assets/js/admin/customizer.js":
/*!***********************************************!*\
  !*** ./redwood/assets/js/admin/customizer.js ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// customize the Customizer
if (wp.customize) {
  // run when the customizer is ready
  wp.customize.bind('ready', function () {
    // we do support custom-header, but we do not honor the
    // Display Site Title and Tagline control, so hide it
    wp.customize.control('display_header_text').toggle(false); // set the visibility of the brand_color control based on the value of the brand_bar control

    wp.customize.control('brand_bar', function (control) {
      var toggleBrandColor = function toggleBrandColor(value) {
        // show if brand_bar's value == 'bar', hide otherwise
        wp.customize.control('brand_color').toggle(value == 'bar');
      }; // initialize the brand_color control based on the current value of brand_bar


      toggleBrandColor(control.setting.get()); // set the visibility of the brand_color control whenever the brand_bar control changes

      control.setting.bind(toggleBrandColor);
    }); // set the visibility of the post_section_title and show_homepage_title controls
    // based on the value of the show_on_front control

    wp.customize.control('show_on_front', function (control) {
      // show / hide controls based on value of show_on_front
      var toggleControls = function toggleControls(value) {
        // show post_section_title if show_on_front's value == 'posts', hide otherwise
        wp.customize.control('post_section_title').toggle(value == 'posts'); // show show_homepage_title if show_on_front's value == 'page', hide otherwise

        wp.customize.control('show_homepage_title').toggle(value == 'page');
      }; // initialize the controls sbased on the current value of show_on_front


      toggleControls(control.setting.get()); // set the visibility of the controls whenever the show_on_front control changes

      control.setting.bind(toggleControls);
    }); // set the visibility of the show_author_avatar, show_ author_website and show_author_email
    // controls based on the value of the show_author_info_inline control

    wp.customize.control('show_author_info_inline', function (control) {
      var toggleAuthorControls = function toggleAuthorControls(value) {
        // show controls if show_author_info_inline is true (checked), hide otherwise
        wp.customize.control('show_author_avatar').toggle(value);
        wp.customize.control('show_author_website').toggle(value);
        wp.customize.control('show_author_email').toggle(value);
      }; // initialize the author controls based on the current value of show_author_info_inline


      toggleAuthorControls(control.setting.get()); // set the visibility of the author controls whenever the show_author_info_inline control changes

      control.setting.bind(toggleAuthorControls);
    });
  }); // acknowledge changes to the GA property id

  wp.customize('ga_property', function (control) {
    var timer = null;
    control.bind(function (value) {
      if (timer) clearTimeout(timer);
      timer = setTimeout(function () {
        var msg = value ? 'Google Analytics property id set to \'' + value + '\'' : 'Google Analytics code will not be included.';
        alert(msg);
      }, 1000);
    });
  });
}

/***/ }),

/***/ "./redwood/assets/js/admin/info_box_widget.js":
/*!****************************************************!*\
  !*** ./redwood/assets/js/admin/info_box_widget.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/**
 * Update the sample icon to match the value entered in the icon input
 * @param {FocusEvent} event
 */
var blurHandler = function blurHandler(event) {
  var target = event.target || event.srcElement; // the element that blurred

  var icon = target.nextElementSibling; // the sample icon

  target.value = target.value.trim(); // trim whitespace

  if (target.value.match(/^fa-[-a-z]+$/)) {
    // if it's (likely to be) a valid icon class
    icon.className = 'fa fa-2x ' + target.value; // apply the class to the sample icon
  } else {
    // if it's not a valid icon class name
    icon.className = ''; // hide the icon
  }
}; // add the blur handler to all the info box widgets on the page at page load time


jQuery('.info_box_widget_icon').blur(blurHandler); // add the blur handler to new widgets when they're added

jQuery(document).on('widget-added', function (event, $widget) {
  $widget.find('.info_box_widget_icon').blur(blurHandler);
});

/***/ })

/******/ });
//# sourceMappingURL=admin.js.map