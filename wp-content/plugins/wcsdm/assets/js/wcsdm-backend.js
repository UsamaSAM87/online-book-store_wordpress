(function($) {
"use strict";

// Returns a function, that, as long as it continues to be invoked, will not
// be triggered. The function will be called after it stops being called for
// N milliseconds.
function debounce(func, wait) {
    var timeout;
    return function () {
        var context = this;
        var args = arguments;
        var later = function () {
            timeout = null;
            func.apply(context, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Replace the percent (%) sign by a variable passed as an argument
 * similar to sprintf function in PHP
 *
 * @see: http://locutus.io/php/strings/sprintf/
 */
function sprintf() {
    var regex = /%%|%(?:(\d+)\$)?((?:[-+#0 ]|'[\s\S])*)(\d+)?(?:\.(\d*))?([\s\S])/g;
    var args = arguments;
    var i = 0;
    var format = args[i++];

    var _pad = function (str, len, chr, leftJustify) {
        if (!chr) {
            chr = ' ';
        }
        var padding = (str.length >= len) ? '' : new Array(1 + len - str.length >>> 0).join(chr);
        return leftJustify ? str + padding : padding + str;
    };

    var justify = function (value, prefix, leftJustify, minWidth, padChar) {
        var diff = minWidth - value.length;
        if (diff > 0) {
            // when padding with zeros
            // on the left side
            // keep sign (+ or -) in front
            if (!leftJustify && padChar === '0') {
                value = [
                    value.slice(0, prefix.length),
                    _pad('', diff, '0', true),
                    value.slice(prefix.length)
                ].join('');
            } else {
                value = _pad(value, minWidth, padChar, leftJustify);
            }
        }

        return value;
    };

    var _formatBaseX = function (value, base, leftJustify, minWidth, precision, padChar) {
        // Note: casts negative numbers to positive ones
        var number = value >>> 0;
        value = _pad(number.toString(base), precision || 0, '0', false);
        return justify(value, '', leftJustify, minWidth, padChar);
    };

    // _formatString()
    var _formatString = function (value, leftJustify, minWidth, precision, customPadChar) {
        if (precision !== null && precision !== undefined) {
            value = value.slice(0, precision);
        }
        return justify(value, '', leftJustify, minWidth, customPadChar);
    };

    // doFormat()
    var doFormat = function (substring, argIndex, modifiers, minWidth, precision, specifier) {
        var number;
        var prefix;
        var method;
        var textTransform;
        var value;

        if (substring === '%%') {
            return '%';
        }

        // parse modifiers
        var padChar = ' '; // pad with spaces by default
        var leftJustify = false;
        var positiveNumberPrefix = '';
        var j, l;

        for (j = 0, l = modifiers.length; j < l; j++) {
            switch (modifiers.charAt(j)) {
                case ' ':
                case '0':
                    padChar = modifiers.charAt(j);
                    break;
                case '+':
                    positiveNumberPrefix = '+';
                    break;
                case '-':
                    leftJustify = true;
                    break;
                case '\'':
                    if (j + 1 < l) {
                        padChar = modifiers.charAt(j + 1);
                        j++;
                    }
                    break;
            }
        }

        if (!minWidth) {
            minWidth = 0;
        } else {
            minWidth = +minWidth;
        }

        if (!isFinite(minWidth)) {
            throw new Error('Width must be finite');
        }

        if (!precision) {
            precision = (specifier === 'd') ? 0 : 'fFeE'.indexOf(specifier) > -1 ? 6 : undefined;
        } else {
            precision = +precision;
        }

        if (argIndex && +argIndex === 0) {
            throw new Error('Argument number must be greater than zero');
        }

        if (argIndex && +argIndex >= args.length) {
            throw new Error('Too few arguments');
        }

        value = argIndex ? args[+argIndex] : args[i++];

        switch (specifier) {
            case '%':
                return '%';
            case 's':
                return _formatString(value + '', leftJustify, minWidth, precision, padChar);
            case 'c':
                return _formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, padChar);
            case 'b':
                return _formatBaseX(value, 2, leftJustify, minWidth, precision, padChar);
            case 'o':
                return _formatBaseX(value, 8, leftJustify, minWidth, precision, padChar);
            case 'x':
                return _formatBaseX(value, 16, leftJustify, minWidth, precision, padChar);
            case 'X':
                return _formatBaseX(value, 16, leftJustify, minWidth, precision, padChar).toUpperCase();
            case 'u':
                return _formatBaseX(value, 10, leftJustify, minWidth, precision, padChar);
            case 'i':
            case 'd':
                number = +value || 0;
                // Plain Math.round doesn't just truncate
                number = Math.round(number - number % 1);
                prefix = number < 0 ? '-' : positiveNumberPrefix;
                value = prefix + _pad(String(Math.abs(number)), precision, '0', false);

                if (leftJustify && padChar === '0') {
                    // can't right-pad 0s on integers
                    padChar = ' ';
                }
                return justify(value, prefix, leftJustify, minWidth, padChar);
            case 'e':
            case 'E':
            case 'f': // @todo: Should handle locales (as per setlocale)
            case 'F':
            case 'g':
            case 'G':
                number = +value;
                prefix = number < 0 ? '-' : positiveNumberPrefix;
                method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(specifier.toLowerCase())];
                textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(specifier) % 2];
                value = prefix + Math.abs(number)[method](precision);
                return justify(value, prefix, leftJustify, minWidth, padChar)[textTransform]();
            default:
                // unknown specifier, consume that char and return empty
                return '';
        }
    };

    try {
        return format.replace(regex, doFormat);
    } catch (err) {
        return false;
    }
}

function toggleBottons(args) {
    'use strict';

    var data = getButton(args);
    $('#wcsdm-buttons').remove();
    $('#btn-ok').hide().after(wp.template('wcsdm-buttons')(data));
}

function getButton(args) {
    'use strict';

    var buttonLabels = wcsdm_backend.i18n.buttons;

    var leftButtonDefaultId = 'add-rate';
    var leftButtonDefaultIcon = 'plus';
    var leftButtonDefaultLabel = 'Add New Rate';

    var leftButtonDefault = {
        id: leftButtonDefaultId,
        icon: leftButtonDefaultIcon,
        label: leftButtonDefaultLabel
    };

    var rightButtonDefaultIcon = 'yes';
    var rightButtonDefaultId = 'save-settings';
    var rightButtonDefaultLabel = 'Save Changes';

    var rightButtonDefault = {
        id: rightButtonDefaultId,
        icon: rightButtonDefaultIcon,
        label: rightButtonDefaultLabel
    };

    var selected = {};
    var leftButton;
    var rightButton;

    if (_.has(args, 'left')) {
        leftButton = _.defaults(args.left, leftButtonDefault);

        if (_.has(buttonLabels, leftButton.label)) {
            leftButton.label = buttonLabels[leftButton.label];
        }

        selected.btn_left = leftButton;
    }

    if (_.has(args, 'right')) {
        rightButton = _.defaults(args.right, rightButtonDefault);

        if (_.has(buttonLabels, rightButton.label)) {
            rightButton.label = buttonLabels[rightButton.label];
        }

        selected.btn_right = rightButton;
    }

    if (_.isEmpty(selected)) {
        leftButton = _.defaults({}, leftButtonDefault);

        if (_.has(buttonLabels, leftButton.label)) {
            leftButton.label = buttonLabels[leftButton.label];
        }

        selected.btn_left = leftButton;

        rightButton = _.defaults({}, rightButtonDefault);

        if (_.has(buttonLabels, rightButton.label)) {
            rightButton.label = buttonLabels[rightButton.label];
        }

        selected.btn_right = rightButton;
    }

    return selected;
}

// Taking Over window.console.error
var isMapError = undefined, isMapErrorInterval;

var windowConsoleError = window.console.error;

window.console.error = function () {
    if (arguments[0].toLowerCase().indexOf('google') !== -1) {
        isMapError = arguments[0];
    }

    windowConsoleError.apply(windowConsoleError, arguments);
};

/**
 * Map Picker
 */
var wcsdmMapPicker = {
    params: {},
    origin_lat: '',
    origin_lng: '',
    origin_address: '',
    zoomLevel: 16,
    apiKeyBrowser: '',
    init: function (params) {
        'use strict';

        wcsdmMapPicker.params = params;

        // Edit Api Key
        $(document).off('click', '.wcsdm-edit-api-key', wcsdmMapPicker.editApiKey);
        $(document).on('click', '.wcsdm-edit-api-key', wcsdmMapPicker.editApiKey);

        // Get API Key
        $(document).off('click', '#wcsdm-btn--get-api-key', wcsdmMapPicker.getApiKey);
        $(document).on('click', '#wcsdm-btn--get-api-key', wcsdmMapPicker.getApiKey);

        // Show Store Location Picker
        $(document).off('click', '.wcsdm-edit-location', wcsdmMapPicker.showStoreLocationPicker);
        $(document).on('click', '.wcsdm-edit-location', wcsdmMapPicker.showStoreLocationPicker);

        // Hide Store Location Picker
        $(document).off('click', '#wcsdm-btn--map-cancel', wcsdmMapPicker.hideStoreLocationPicker);
        $(document).on('click', '#wcsdm-btn--map-cancel', wcsdmMapPicker.hideStoreLocationPicker);

        // Apply Store Location
        $(document).off('click', '#wcsdm-btn--map-apply', wcsdmMapPicker.applyStoreLocation);
        $(document).on('click', '#wcsdm-btn--map-apply', wcsdmMapPicker.applyStoreLocation);

        // Toggle Map Search Panel
        $(document).off('click', '#wcsdm-map-search-panel-toggle', wcsdmMapPicker.toggleMapSearch);
        $(document).on('click', '#wcsdm-map-search-panel-toggle', wcsdmMapPicker.toggleMapSearch);
    },
    testDistanceMatrix: function () {
        var origin = new google.maps.LatLng(parseFloat(wcsdmMapPicker.params.defaultLat), parseFloat(wcsdmMapPicker.params.defaultLng));
        var destination = new google.maps.LatLng(parseFloat(wcsdmMapPicker.params.testLat), parseFloat(wcsdmMapPicker.params.testLng));
        var service = new google.maps.DistanceMatrixService();

        service.getDistanceMatrix(
            {
                origins: [origin],
                destinations: [destination],
                travelMode: 'DRIVING',
                unitSystem: google.maps.UnitSystem.METRIC
            }, function (response, status) {
                if (status.toLowerCase() === 'ok') {
                    isMapError = false;
                } else {
                    if (response.error_message) {
                        isMapError = response.error_message;
                    } else {
                        isMapError = 'Error: ' + status;
                    }
                }
            });
    },
    editApiKey: function (e) {
        'use strict';

        e.preventDefault();

        var $link = $(e.currentTarget);
        var $input = $link.closest('tr').find('input[type=hidden]');
        var $inputDummy = $link.closest('tr').find('input[type=text]');
        var apiKey = $input.val();
        var apiKeyDummy = $inputDummy.val();

        if ($link.hasClass('editing')) {
            if (apiKey !== apiKeyDummy) {
                $link.addClass('loading').attr('disabled', true);

                switch ($link.attr('id')) {
                    case 'api_key_browser': {
                        wcsdmMapPicker.initMap(apiKeyDummy, wcsdmMapPicker.testDistanceMatrix);

                        clearInterval(isMapErrorInterval);

                        isMapErrorInterval = setInterval(function () {
                            if (typeof isMapError !== 'undefined') {
                                clearInterval(isMapErrorInterval);

                                if (isMapError) {
                                    $inputDummy.val(apiKey);
                                    window.alert(isMapError);
                                } else {
                                    $input.val(apiKeyDummy);
                                }

                                $link.removeClass('loading editing').attr('disabled', false);
                                $inputDummy.prop('readonly', true);
                            }
                        }, 100);
                        break;
                    }

                    default: {
                        $.ajax({
                            method: "POST",
                            url: wcsdmMapPicker.params.ajax_url,
                            data: {
                                action: "wcsdm_validate_api_key_server",
                                nonce: wcsdmMapPicker.params.validate_api_key_nonce,
                                key: apiKeyDummy,
                            }
                        }).done(function () {
                            // Set new API Key value
                            $input.val(apiKeyDummy);
                        }).fail(function (error) {
                            // Restore existing API Key value
                            $inputDummy.val(apiKey);

                            // Show error
                            if (error.responseJSON && error.responseJSON.data) {
                                return window.alert(error.responseJSON.data);
                            }

                            if (error.statusText) {
                                return window.alert(error.statusText);
                            }

                            window.alert('Error');
                        }).always(function () {
                            $link.removeClass('loading editing').attr('disabled', false);
                            $inputDummy.prop('readonly', true);
                        });
                    }
                }
            } else {
                $link.removeClass('editing');
                $inputDummy.prop('readonly', true);
            }
        } else {
            $link.addClass('editing');
            $inputDummy.prop('readonly', false);
        }
    },
    getApiKey: function (e) {
        'use strict';

        e.preventDefault();

        window.open('https://cloud.google.com/maps-platform/#get-started', '_blank').focus();
    },
    showStoreLocationPicker: function (e) {
        'use strict';

        e.preventDefault();

        $('.modal-close-link').hide();

        toggleBottons({
            left: {
                id: 'map-cancel',
                label: 'Cancel',
                icon: 'undo'
            },
            right: {
                id: 'map-apply',
                label: 'Apply Changes',
                icon: 'editor-spellcheck'
            }
        });

        $('#wcsdm-field-group-wrap--location_picker').fadeIn().siblings().hide();

        wcsdmMapPicker.initMap($('#woocommerce_wcsdm_api_key_browser').val(), wcsdmMapPicker.renderMap);
    },
    hideStoreLocationPicker: function (e) {
        'use strict';

        e.preventDefault();

        wcsdmMapPicker.destroyMap();

        $('.modal-close-link').show();

        toggleBottons();

        $('#wcsdm-field-group-wrap--location_picker').hide().siblings().not('.wcsdm-hidden').fadeIn();
    },
    applyStoreLocation: function (e) {
        'use strict';

        e.preventDefault();

        if (isMapError) {
            return;
        }

        wcsdmMapPicker.initMap($('#woocommerce_wcsdm_api_key_browser').val(), wcsdmMapPicker.testDistanceMatrix);

        clearInterval(isMapErrorInterval);

        isMapErrorInterval = setInterval(function () {
            if (typeof isMapError !== 'undefined') {
                clearInterval(isMapErrorInterval);

                if (isMapError) {
                    window.alert(isMapError);
                } else {
                    $('#woocommerce_wcsdm_origin_lat').val(wcsdmMapPicker.origin_lat);
                    $('#woocommerce_wcsdm_origin_lng').val(wcsdmMapPicker.origin_lng);
                    $('#woocommerce_wcsdm_origin_address').val(wcsdmMapPicker.origin_address);
                    wcsdmMapPicker.hideStoreLocationPicker(e);
                }
            }
        }, 100);
    },
    toggleMapSearch: function (e) {
        'use strict';

        e.preventDefault();

        $(e.currentTarget).find('span').toggleClass('dashicons-search').toggleClass('dashicons-dismiss');

        $('#wcsdm-map-search-panel').toggleClass('hide-main');
        $('#wcsdm-map-search-panel-main').toggleClass('wcsdm-hidden');
    },
    initMap: function (apiKey, callback) {
        wcsdmMapPicker.destroyMap();

        isMapError = undefined;

        if (_.isEmpty(apiKey)) {
            apiKey = 'InvalidKey';
        }

        $.getScript('https://maps.googleapis.com/maps/api/js?libraries=geometry,places&key=' + apiKey, callback);
    },
    renderMap: function () {
        wcsdmMapPicker.origin_lat = $('#woocommerce_wcsdm_origin_lat').val();
        wcsdmMapPicker.origin_lng = $('#woocommerce_wcsdm_origin_lng').val();

        var currentLatLng = {
            lat: _.isEmpty(wcsdmMapPicker.origin_lat) ? parseFloat(wcsdmMapPicker.params.defaultLat) : parseFloat(wcsdmMapPicker.origin_lat),
            lng: _.isEmpty(wcsdmMapPicker.origin_lng) ? parseFloat(wcsdmMapPicker.params.defaultLng) : parseFloat(wcsdmMapPicker.origin_lng)
        };

        var map = new google.maps.Map(
            document.getElementById('wcsdm-map-canvas'),
            {
                mapTypeId: 'roadmap',
                center: currentLatLng,
                zoom: wcsdmMapPicker.zoomLevel,
                streetViewControl: false,
                mapTypeControl: false
            }
        );

        var marker = new google.maps.Marker({
            map: map,
            position: currentLatLng,
            draggable: true,
            icon: wcsdmMapPicker.params.marker
        });

        var infowindow = new google.maps.InfoWindow({ maxWidth: 350 });

        if (_.isEmpty(wcsdmMapPicker.origin_lat) || _.isEmpty(wcsdmMapPicker.origin_lng)) {
            infowindow.setContent(wcsdmMapPicker.params.i18n.drag_marker);
            infowindow.open(map, marker);
        } else {
            wcsdmMapPicker.setLatLng(marker.position, marker, map, infowindow);
        }

        google.maps.event.addListener(marker, 'dragstart', function () {
            infowindow.close();
        });

        google.maps.event.addListener(marker, 'dragend', function (event) {
            wcsdmMapPicker.setLatLng(event.latLng, marker, map, infowindow);
        });

        $('#wcsdm-map-wrap').prepend(wp.template('wcsdm-map-search-panel')());
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(document.getElementById('wcsdm-map-search-panel'));

        var mapSearchBox = new google.maps.places.SearchBox(document.getElementById('wcsdm-map-search-input'));

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function () {
            mapSearchBox.setBounds(map.getBounds());
        });

        var markers = [];

        // Listen for the event fired when the user selects a prediction and retrieve more details for that place.
        mapSearchBox.addListener('places_changed', function () {
            var places = mapSearchBox.getPlaces();
            if (places.length === 0) {
                return;
            }

            // Clear out the old markers.
            markers.forEach(function (marker) {
                marker.setMap(null);
            });

            markers = [];

            // For each place, get the icon, name and location.
            var bounds = new google.maps.LatLngBounds();

            places.forEach(function (place) {
                if (!place.geometry) {
                    console.log('Returned place contains no geometry');
                    return;
                }

                marker = new google.maps.Marker({
                    map: map,
                    position: place.geometry.location,
                    draggable: true,
                    icon: wcsdmMapPicker.params.marker
                });

                wcsdmMapPicker.setLatLng(place.geometry.location, marker, map, infowindow);

                google.maps.event.addListener(marker, 'dragstart', function () {
                    infowindow.close();
                });

                google.maps.event.addListener(marker, 'dragend', function (event) {
                    wcsdmMapPicker.setLatLng(event.latLng, marker, map, infowindow);
                });

                // Create a marker for each place.
                markers.push(marker);

                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });

            map.fitBounds(bounds);
        });
    },
    destroyMap: function () {
        if (window.google) {
            window.google = undefined;
        }

        $('#wcsdm-map-canvas').empty();
        $('#wcsdm-map-search-panel').remove();
    },
    setLatLng: function (location, marker, map, infowindow) {
        var geocoder = new google.maps.Geocoder();

        geocoder.geocode(
            {
                latLng: location
            },
            function (results, status) {
                if (status === google.maps.GeocoderStatus.OK && results[0]) {
                    var infowindowContents = [
                        wcsdmMapPicker.params.i18n.latitude + ': ' + location.lat().toString(),
                        wcsdmMapPicker.params.i18n.longitude + ': ' + location.lng().toString()
                    ];

                    infowindow.setContent(infowindowContents.join('<br />'));
                    infowindow.open(map, marker);

                    marker.addListener('click', function () {
                        infowindow.open(map, marker);
                    });

                    $('#wcsdm-map-search-input').val(results[0].formatted_address);

                    wcsdmMapPicker.origin_lat = location.lat();
                    wcsdmMapPicker.origin_lng = location.lng();
                    wcsdmMapPicker.origin_address = results[0].formatted_address;
                }
            }
        );

        map.setCenter(location);
    }
};

/**
 * Table Rates
 */
var wcsdmTableRates = {
    params: {},
    errorId: 'wcsdm-errors-rate-fields',
    init: function (params) {
        'use strict';
        wcsdmTableRates.params = params;

        // Show advanced rate form
        $(document).off('click', '.wcsdm-link--advanced-rate', wcsdmTableRates.showAdvancedRateForm);
        $(document).on('click', '.wcsdm-link--advanced-rate', wcsdmTableRates.showAdvancedRateForm);

        // Close advanced rate form
        $(document).off('click', '#wcsdm-btn--cancel-advanced', wcsdmTableRates.closeAdvancedRateForm);
        $(document).on('click', '#wcsdm-btn--cancel-advanced', wcsdmTableRates.closeAdvancedRateForm);

        // Apply advanced rate
        $(document).off('click', '#wcsdm-btn--apply-advanced', wcsdmTableRates.applyAdvancedForm);
        $(document).on('click', '#wcsdm-btn--apply-advanced', wcsdmTableRates.applyAdvancedForm);

        // Add rate row
        $(document).off('click', '#wcsdm-btn--add-rate', wcsdmTableRates.handleAddRateButton);
        $(document).on('click', '#wcsdm-btn--add-rate', wcsdmTableRates.handleAddRateButton);

        // Delete rate row
        $(document).off('click', '#wcsdm-btn--delete-rate-select', wcsdmTableRates.deleteRateRow);
        $(document).on('click', '#wcsdm-btn--delete-rate-select', wcsdmTableRates.deleteRateRow);

        // Cancel delete rate row
        $(document).off('click', '#wcsdm-btn--delete-rate-cancel', wcsdmTableRates.deleteRateRowCancel);
        $(document).on('click', '#wcsdm-btn--delete-rate-cancel', wcsdmTableRates.deleteRateRowCancel);

        // Confirm delete rate row
        $(document).off('click', '#wcsdm-btn--delete-rate-confirm', wcsdmTableRates.deleteRateRowConfirm);
        $(document).on('click', '#wcsdm-btn--delete-rate-confirm', wcsdmTableRates.deleteRateRowConfirm);

        // Toggle selected rows
        $(document).off('change', '#wcsdm-table--table_rates--dummy thead .select-item', wcsdmTableRates.toggleRows);
        $(document).on('change', '#wcsdm-table--table_rates--dummy thead .select-item', wcsdmTableRates.toggleRows);

        // Toggle selected row
        $(document).off('change', '#wcsdm-table--table_rates--dummy tbody .select-item', wcsdmTableRates.toggleRow);
        $(document).on('change', '#wcsdm-table--table_rates--dummy tbody .select-item', wcsdmTableRates.toggleRow);

        // Handle change event dummy rate field
        $(document).off('input', '.wcsdm-field--context--dummy:not(a)');
        $(document).on('input', '.wcsdm-field--context--dummy:not(a)', debounce(function (e) {
            wcsdmTableRates.handleRateFieldDummy(e);
        }, 500));

        // Toggle selected row
        $(document).off('change', '#woocommerce_wcsdm_distance_unit', wcsdmTableRates.initForm);
        $(document).on('change', '#woocommerce_wcsdm_distance_unit', wcsdmTableRates.initForm);

        wcsdmTableRates.initForm();

        if (!$('#wcsdm-table--table_rates--dummy tbody tr').length) {
            wcsdmTableRates.addRateRow();
        }
    },
    initForm: function () {
        'use strict';

        $('.wcsdm-field-key--total_cost_type > option[value="formula"]').prop('disabled', !wcsdmTableRates.params.isPro);

        var distanceUnitSelected = $('#woocommerce_wcsdm_distance_unit').val();
        var $distanceUnitFields = $('#woocommerce_wcsdm_distance_unit').data('fields');

        var label = $distanceUnitFields && _.has($distanceUnitFields.label, distanceUnitSelected) ? $distanceUnitFields.label[distanceUnitSelected] : '';

        if (label && label.length) {
            _.each($distanceUnitFields.targets, function (target) {
                $(target).text(label);
            });
        }
    },
    handleAddRateButton: function (e) {
        'use strict';
        e.preventDefault();
        $(e.currentTarget).prop('disabled', true);

        wcsdmTableRates.addRateRow();

        $(e.currentTarget).prop('disabled', false);
    },
    handleRateFieldDummy: function (e) {
        'use strict';
        e.preventDefault();

        var $field = $(e.target);
        var $row = $field.closest('tr');
        $row.find('.wcsdm-field--context--hidden[data-id=' + $field.data('id') + ']').val(e.target.value);

        if ($field.hasClass('wcsdm-field--context--dummy--max_distance')) {
            $row.addClass('editing');
            $field.off('blur', wcsdmTableRates.sortRateRows);
            $field.on('blur', wcsdmTableRates.sortRateRows);
        }
    },
    showAdvancedRateForm: function (e) {
        'use strict';
        e.preventDefault();

        $('#wcsdm-table--table_rates--dummy .select-item').prop('checked', false);
        $('#wcsdm-table--table_rates--dummy tbody tr').removeClass();

        var $row = $(e.currentTarget).closest('tr').addClass('editing');

        $row.find('.wcsdm-field--context--hidden').each(function (index, field) {
            $('.wcsdm-field--context--advanced[data-id=' + $(field).data('id') + ']').val($(field).val());
        });

        toggleBottons({
            left: {
                id: 'cancel-advanced',
                label: 'Cancel',
                icon: 'undo'
            },
            right: {
                id: 'apply-advanced',
                label: 'Apply Changes',
                icon: 'editor-spellcheck'
            }
        });

        $('.modal-close-link').hide();

        $('#wcsdm-field-group-wrap--advanced_rate').fadeIn().siblings('.wcsdm-field-group-wrap').hide();
    },
    applyAdvancedForm: function (e) {
        'use strict';
        e.preventDefault();

        $('.wcsdm-field--context--advanced').each(function (index, field) {
            var fieldId = $(field).data('id');
            $('#wcsdm-table--table_rates--dummy tbody tr.editing .wcsdm-field--context--dummy[data-id=' + fieldId + ']:not(a)').val($(field).val());
            $('#wcsdm-table--table_rates--dummy tbody tr.editing .wcsdm-field--context--hidden[data-id=' + fieldId + ']:not(a)').val($(field).val());
        });

        wcsdmTableRates.closeAdvancedRateForm(e);
    },
    closeAdvancedRateForm: function (e) {
        'use strict';
        e.preventDefault();
        $('#wcsdm-field-group-wrap--advanced_rate').hide().siblings('.wcsdm-field-group-wrap').not('.wcsdm-hidden').fadeIn();
        toggleBottons();
        $('.modal-close-link').show();
        wcsdmTableRates.scrollToTableRate();
        wcsdmTableRates.sortRateRows();
    },
    highlightRow: function () {
        'use strict';
        var $row = $('#wcsdm-table--table_rates--dummy tbody tr.editing');
        if ($row.length) {
            $row.addClass('highlighted');
            setTimeout(function () {
                $row.removeClass('highlighted');
            }, 1500);
        }
        $('#wcsdm-table--table_rates--dummy tbody tr').removeClass('editing');
    },
    addRateRow: function () {
        'use strict';
        var $lastRow = $('#wcsdm-table--table_rates--dummy tbody tr:last-child');

        $('#wcsdm-table--table_rates--dummy tbody').append(wp.template('wcsdm-dummy-row'));

        if ($lastRow) {
            $lastRow.find('.wcsdm-field--context--hidden:not(a)').each(function (index, field) {
                var fieldId = $(field).data('id');
                var fieldValue = fieldId === 'woocommerce_wcsdm_max_distance' ? Math.ceil((parseInt($(field).val(), 10) * 1.8)) : $(field).val();
                $('#wcsdm-table--table_rates--dummy tbody tr:last-child .wcsdm-field[data-id=' + fieldId + ']').val(fieldValue);
            });
        }

        wcsdmTableRates.scrollToTableRate();

        wcsdmTableRates.initForm();
    },
    deleteRateRow: function (e) {
        'use strict';
        e.preventDefault();

        $('#wcsdm-table--table_rates--dummy tbody .select-item:not(:checked)').closest('tr').hide();
        $('#wcsdm-table--table_rates--dummy').find('.wcsdm-col--select-item, .wcsdm-col--link_advanced').hide();
        $('#wcsdm-field-group-wrap--table_rates').siblings().hide();

        wcsdmTableRates.scrollToTableRate();

        toggleBottons({
            left: {
                id: 'delete-rate-cancel',
                label: 'Cancel',
                icon: 'undo'
            },
            right: {
                id: 'delete-rate-confirm',
                label: 'Confirm Delete',
                icon: 'trash'
            }
        });
    },
    deleteRateRowCancel: function (e) {
        'use strict';
        e.preventDefault();

        $('#wcsdm-table--table_rates--dummy tbody tr').show();
        $('#wcsdm-table--table_rates--dummy').find('.wcsdm-col--select-item, .wcsdm-col--link_advanced').show();
        $('#wcsdm-field-group-wrap--table_rates').siblings().not('.wcsdm-hidden').fadeIn();

        wcsdmTableRates.scrollToTableRate();

        toggleBottons({
            left: {
                id: 'delete-rate-select',
                label: 'Delete Selected Rates',
                icon: 'trash'
            }
        });
    },
    deleteRateRowConfirm: function (e) {
        'use strict';
        e.preventDefault();

        $('#wcsdm-table--table_rates--dummy tbody .select-item:checked').closest('tr').remove();
        $('#wcsdm-table--table_rates--dummy tbody tr').show();
        $('#wcsdm-table--table_rates--dummy').find('.wcsdm-col--select-item, .wcsdm-col--link_advanced').show();
        $('#wcsdm-table--table_rates--dummy .select-item').prop('checked', false);
        $('#wcsdm-field-group-wrap--table_rates').siblings().not('.wcsdm-hidden').fadeIn();

        toggleBottons();

        if (!$('#wcsdm-table--table_rates--dummy tbody tr').length) {
            wcsdmTableRates.addRateRow();
        }
    },
    toggleRows: function (e) {
        'use strict';
        e.preventDefault();

        $.each($('#wcsdm-table--table_rates--dummy tbody tr'), function (index, row) {
            wcsdmTableRates.toggleRowSelected($(row), $(e.target).is(':checked'));
        });

        if ($(e.target).is(':checked')) {
            toggleBottons({
                left: {
                    id: 'delete-rate-select',
                    label: 'Delete Selected Rates',
                    icon: 'trash'
                }
            });
        } else {
            toggleBottons();
        }
    },
    toggleRow: function (e) {
        'use strict';
        e.preventDefault();

        var $field = $(e.target);
        var $row = $(e.target).closest('tr');

        wcsdmTableRates.toggleRowSelected($row, $field.is(':checked'));

        if ($('#wcsdm-table--table_rates--dummy tbody .select-item:checked').length) {
            toggleBottons({
                left: {
                    id: 'delete-rate-select',
                    label: 'Delete Selected Rates',
                    icon: 'trash'
                }
            });
        } else {
            toggleBottons();
        }

        var isBulkChecked = $('#wcsdm-table--table_rates--dummy tbody .select-item').length === $('#wcsdm-table--table_rates--dummy tbody .select-item:checked').length;

        $('#wcsdm-table--table_rates--dummy thead .select-item').prop('checked', isBulkChecked);
    },
    toggleRowSelected: function ($row, isChecked) {
        $row.find('.wcsdm-field--context--dummy').prop('disabled', isChecked);

        if (isChecked) {
            $row.addClass('selected').find('.select-item').prop('checked', isChecked);
            $row.find('a').css('opacity', '0.4');
        } else {
            $row.removeClass('selected').find('.select-item').prop('checked', isChecked);
            $row.find('a').css('opacity', '1');
        }
    },
    sortRateRows: function () {
        'use strict';
        var rows = $('#wcsdm-table--table_rates--dummy > tbody > tr').get().sort(function (a, b) {
            var valueADistance = $(a).find('.wcsdm-field--context--dummy--max_distance').val();
            var valueBDistance = $(b).find('.wcsdm-field--context--dummy--max_distance').val();

            if (isNaN(valueADistance) || !valueADistance.length) {
                return 2;
            }

            valueADistance = parseInt(valueADistance, 10);
            valueBDistance = parseInt(valueBDistance, 10);

            if (valueADistance < valueBDistance) {
                return -1;
            }

            if (valueADistance > valueBDistance) {
                return 1;
            }

            return 0;
        });

        $.each(rows, function (index, row) {
            var $row = $(row).hide();
            $('#wcsdm-table--table_rates--dummy').children('tbody').append(row);
            $row.fadeIn('slow');
        });

        setTimeout(function () {
            wcsdmTableRates.highlightRow();
        }, 100);
    },
    scrollToTableRate: function () {
        $('.wc-modal-shipping-method-settings').scrollTop($('.wc-modal-shipping-method-settings').find('form').outerHeight());
    }
};

/**
 * Backend Scripts
 */

var wcsdmBackend = {
    renderForm: function () {
        if (!$('#woocommerce_wcsdm_origin_type') || !$('#woocommerce_wcsdm_origin_type').length) {
            return;
        }

        // Submit form
        $(document).off('click', '#wcsdm-btn--save-settings', wcsdmBackend.submitForm);
        $(document).on('click', '#wcsdm-btn--save-settings', wcsdmBackend.submitForm);

        // Show API Key instruction
        $(document).off('click', '.wcsdm-show-instructions', wcsdmBackend.showApiKeyInstructions);
        $(document).on('click', '.wcsdm-show-instructions', wcsdmBackend.showApiKeyInstructions);

        // Close API Key instruction
        $(document).off('click', '#wcsdm-btn--close-instructions', wcsdmBackend.closeApiKeyInstructions);
        $(document).on('click', '#wcsdm-btn--close-instructions', wcsdmBackend.closeApiKeyInstructions);

        // Toggle Store Origin Fields
        $(document).off('change', '#woocommerce_wcsdm_origin_type', wcsdmBackend.toggleStoreOriginFields);
        $(document).on('change', '#woocommerce_wcsdm_origin_type', wcsdmBackend.toggleStoreOriginFields);

        $('#woocommerce_wcsdm_origin_type').trigger('change');

        $('.wc-modal-shipping-method-settings table.form-table').each(function () {
            var $table = $(this);
            var $rows = $table.find('tr');
            if (!$rows.length) {
                $table.remove();
            }
        });

        $('.wcsdm-field-group').each(function () {
            var $fieldGroup = $(this);

            var fieldGroupId = $fieldGroup
                .attr('id')
                .replace('woocommerce_wcsdm_field_group_', '');

            var $fieldGroupDescription = $fieldGroup
                .next('p')
                .detach();

            var $fieldGroupTable = $fieldGroup
                .nextAll('table.form-table')
                .first()
                .attr('id', 'wcsdm-table--' + fieldGroupId)
                .addClass('wcsdm-table wcsdm-table--' + fieldGroupId)
                .detach();

            $fieldGroup
                .wrap('<div id="wcsdm-field-group-wrap--' + fieldGroupId + '" class="wcsdm-field-group-wrap wcsdm-field-group-wrap--' + fieldGroupId + '"></div>');

            $fieldGroupDescription
                .appendTo('#wcsdm-field-group-wrap--' + fieldGroupId);

            $fieldGroupTable
                .appendTo('#wcsdm-field-group-wrap--' + fieldGroupId);

            if ($fieldGroup.hasClass('wcsdm-field-group-hidden')) {
                $('#wcsdm-field-group-wrap--' + fieldGroupId)
                    .addClass('wcsdm-hidden');
            }
        });

        var params = _.mapObject(wcsdm_backend, function (val, key) {
            switch (key) {
                case 'default_lat':
                case 'default_lng':
                case 'test_destination_lat':
                case 'test_destination_lng':
                    return parseFloat(val);

                default:
                    return val;
            }
        });

        wcsdmTableRates.init(params);
        wcsdmMapPicker.init(params);

        toggleBottons();
    },
    maybeOpenModal: function () {
        // Try show settings modal on settings page.
        if (wcsdm_backend.showSettings) {
            setTimeout(function () {
                var isMethodAdded = false;
                var methods = $(document).find('.wc-shipping-zone-method-type');
                for (var i = 0; i < methods.length; i++) {
                    var method = methods[i];
                    if ($(method).text() === wcsdm_backend.methodTitle) {
                        $(method).closest('tr').find('.row-actions .wc-shipping-zone-method-settings').trigger('click');
                        isMethodAdded = true;
                        return;
                    }
                }

                // Show Add shipping method modal if the shipping is not added.
                if (!isMethodAdded) {
                    $('.wc-shipping-zone-add-method').trigger('click');
                    $('select[name="add_method_id"]').val(wcsdm_backend.methodId).trigger('change');
                }
            }, 500);
        }
    },
    submitForm: function (e) {
        'use strict';
        e.preventDefault();

        $('#btn-ok').trigger('click');
    },
    showApiKeyInstructions: function (e) {
        'use strict';

        e.preventDefault();

        toggleBottons({
            left: {
                id: 'close-instructions',
                label: 'Back',
                icon: 'undo'
            },
            right: {
                id: 'get-api-key',
                label: 'Get API Key',
                icon: 'admin-links'
            }
        });

        $('#wcsdm-field-group-wrap--api_key_instruction').fadeIn().siblings().hide();

        $('.modal-close-link').hide();
    },
    closeApiKeyInstructions: function (e) {
        'use strict';

        e.preventDefault();

        $('#wcsdm-field-group-wrap--api_key_instruction').hide().siblings().not('.wcsdm-hidden').fadeIn();

        $('.modal-close-link').show();

        toggleBottons();
    },
    toggleStoreOriginFields: function (e) {
        e.preventDefault();
        var selected = $(this).val();
        var fields = $(this).data('fields');
        _.each(fields, function (fieldIds, fieldValue) {
            _.each(fieldIds, function (fieldId) {
                if (fieldValue !== selected) {
                    $('#' + fieldId).closest('tr').hide();
                } else {
                    $('#' + fieldId).closest('tr').show();
                }
            });
        });
    },
    initForm: function () {
        // Init form
        $(document.body).off('wc_backbone_modal_loaded', wcsdmBackend.renderForm);
        $(document.body).on('wc_backbone_modal_loaded', wcsdmBackend.renderForm);
    },
    init: function () {
        wcsdmBackend.initForm();
        wcsdmBackend.maybeOpenModal();
    }
};

$(document).ready(wcsdmBackend.init);
}(jQuery));
