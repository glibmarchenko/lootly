require('./_partials/navbar');
require('./_partials/sidebar');
require('./_partials/color-picker');
require('./_partials/select-color-picker');


Vue.filter('date-format', function (date) {
    if (!date) return '';
    var local = moment.utc(date).local().format('MM/DD/YYYY');
    return local;
});

Vue.filter('date-human', function (date) {
    if (!date) return '';
    moment.locale('en', {
        relativeTime: {
            future: 'in %s',
            past: '%s ago',
            s:  '%d seconds',
            ss: '%ss',
            m:  'a minute',
            mm: '%d minutes',
            h:  'an hour',
            hh: '%d hours',
            d:  'a day',
            dd: '%d days',
            M:  'a month',
            MM: '%d months',
            y:  'a year',
            yy: '%d years'
        }
    });
    var local = moment.utc(date).local().fromNow();
    return local;
});

Vue.filter('capitalize', function (value) {
    if (!value) return '';
    value = value.toString();
    return value.charAt(0).toUpperCase() + value.slice(1)
});

Vue.filter('currency', function (value, sign, dysplayBefore = true) {
    // var formatter = new Intl.NumberFormat('en-US', {
    //     style: 'currency',
    //     currency: 'USD',
    //     minimumFractionDigits: 0
    // });
    // return formatter.format(value).replace('$', sign);

    return dysplayBefore ? sign + value : value + sign;
});

Vue.filter('format-number', function (value) {
    let shortValue = parseFloat(value);
    let symbol = '';
    if(shortValue % 1 !== 0){ // check if float
        shortValue = shortValue.toFixed(2);
    }

    if(value > 1000000){
        shortValue = (value / 1000000).toFixed(2);
        symbol = 'm';
    } else if(value > 10000){
        shortValue = (value / 1000).toFixed(2);
        symbol = 'k';
    }
    return shortValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + symbol;
});

Vue.filter('to-float', function (value) {
    value = parseFloat(value) 
    if(value % 1 !== 0){ // check if float
        value = value.toFixed(2);
    }

    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
});

Array.prototype.remove = function (key, value) {
    const index = this.findIndex(obj => obj[key] === value);
    return index >= 0 ? [
        ...this.slice(0, index),
        ...this.slice(index + 1)
    ] : this;
};

Array.prototype.removeByIndex = function (index) {
    return index >= 0 ? [
        ...this.slice(0, index),
        ...this.slice(index + 1)
    ] : this;
};

Array.prototype.getText = function (key, value) {
    const index = this.findIndex(obj => obj[key] === value);
    return this[index].text;
};

Array.prototype.getObjectByKey = function (key, value) {
    const index = this.findIndex(obj => obj[key] === value);
    return this[index];
};

// Error Clear
function clearErrors(form) {
    if (form) {
        $(form).find(".alert-danger").remove();
    }
    else {
        $(".alert-danger").remove();
    }
}

window.clearErrors = clearErrors;

// Error Show
function showErrors(form, errors) {
    let errorHintTpl = '<span class="alert-danger form-control"></span>';

    if (errors)
        $.map(errors, function (error, name) {
            if(typeof error === 'array') error = error[0].replace(/\./g, ' ')
            let $errorHint = $(errorHintTpl).html(error),
                $inputElement = $(form).find("[name='" + name + "']").first();
            if ($inputElement) {
                $inputElement.parent().append($errorHint);

                $inputElement.click(function () {
                    $errorHint.remove();
                });

                $inputElement.focus(function () {
                    $errorHint.remove();
                });
            }
        });
}

window.showErrors = showErrors;

// Show Preview Icon

function showPreviewIcon(icon, default_class, elm_class) {
    let iconHintTpl = '<img name="icon_image" src="' + icon + '" style="max-width: 45px;">';


    let defaultIconHintTpl = '<i  class="' + default_class + '"></i>';
    let parentElement = $('.' + elm_class);
    if (icon) {
        parentElement.append(iconHintTpl)
    } else {
        // if (elm_class == 'goal-spend') {
        //     let goal = '<img name="icon_image"  style="max-width: 30px;" src="../../../images/icons/flag.png">';
        //     $('.' + elm_class).append(goal)
        // } else {
        $('.' + elm_class).append(defaultIconHintTpl)
        // }
    }
}


window.showPreviewIcon = showPreviewIcon;

// Clear Preview Icon

function clearPreviewIcon(default_class, elm_class) {
    let parentElement = $('.' + elm_class);
    let name = 'icon_image';
    parentElement.find("[name='" + name + "']").remove();
    parentElement.find('.' + default_class).remove();
}

window.clearPreviewIcon = clearPreviewIcon;

function resetForm($el) {

    $el.wrap('<form>').closest('form').get(0).reset();
    $el.unwrap();
}
window.resetForm = resetForm;

function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    var items = location.search.substr(1).split("&");
    for (var index = 0; index < items.length; index++) {
        tmp = items[index].split("=");
        if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
    }
    return result;
}
window.findGetParameter = findGetParameter;

function iconImageChange(event) {
    var $this = this;
    var files = evt.target.files;
    var f = files[0];
    this.form.program.icon_name = f.name;
    var reader = new FileReader();

    reader.onload = (function (theFile) {
        return function (e) {
            this.form.iconPreview = e.target.result;
        };

    })(f);

    reader.readAsDataURL(f);
}

window.iconImageChange = iconImageChange;

function formatCurrency(currency, value, displaySign) {
    return displaySign ? currency + '' + value : value + ' ' + currency;
}

window.formatCurrency = formatCurrency;

function prefixCssSelectors(rules, className) {
    var classLen = className.length, char, nextChar, isAt, isIn;

    className += ' ';

    rules = rules.replace(/\/\*(?:(?!\*\/)[\s\S])*\*\/|[\r\n\t]+/g, '');

    rules = rules.replace(/}(\s*)@/g, '}@');
    rules = rules.replace(/}(\s*)}/g, '}}');

    for (var i = 0; i < rules.length - 2; i++) {
        char = rules[i];
        nextChar = rules[i + 1];

        if (char === '@') isAt = true;
        if (!isAt && char === '{') isIn = true;
        if (isIn && char === '}') isIn = false;

        if (
            !isIn &&
            nextChar !== '@' &&
            nextChar !== '}' &&
            (
                char === '}' ||
                char === ',' ||
                ((char === '{' || char === ';') && isAt)
            )
        ) {
            rules = rules.slice(0, i + 1) + className + rules.slice(i + 1);
            i += classLen;
            isAt = false;
        }
    };

    // prefix the first select if it is not `@media` and if it is not yet prefixed
    if (rules.indexOf(className) !== 0 && rules.indexOf('@') !== 0) rules = className + rules;

    return rules;
}

window.prefixCssSelectors = prefixCssSelectors;
