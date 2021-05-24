let lang = jQuery('html').attr('lang');
let current_api = null;
let strServiceId = null;
let strApiToken = null;
let iAmount = null;
let boolToggle = true;
let iTransactionId = null;
let transactionUUID = null;
let paymentTransactionId = null;
let strTerminalId = null;
let strDescription = null;
let strOrderNumber = null;
let strStatusApiUrl = null;
let strCancelApiUrl = null;
let strHash = null;
let strStatusPayment = null;
let strTerminalStatus = null;
let transactionTimer = 0;
let boolStaticFields = false;
let errorMessage = null;
let cardNumberType = null;
let animateLoading = null;
let pinCode = null;
let transactionInterval = null;
let rStatusTimeout;
let balance = null;
let scanStartAllow = false;
let rProgressTimeout;
let iStatusTimeout = 1500;
let iProgressTimeout = 150;
let scanCardImageCode = 1645;
let aliPayImageCode = 2080;
let weChatImageCode = 1978;
let imageQRPattern = 'https://static.pay.nl/app/qr-{image_code}.png';
let imageQRPaymentMethodPattern = 'https://static.pay.nl/app/pp/qr-{image_code}.png';
let imagePattern = 'https://static.pay.nl/payment_profiles/75x75/{image_code}.png';
let cardNumber = null;
let arrStyleProgress = {
    approved: 'progress-bar-success',
    declined: 'progress-bar-danger',
    cancelled: 'progress-bar-warning',
    verify: 'progress-bar-warning',
    init: 'progress-bar',
    error: 'progress-bar-danger',
    expired: 'progress-bar-danger'
};
let arrStyleStatus = {
    approved: 'status-approved',
    declined: 'status-declined',
    cancelled: 'status-cancelled',
    verify: 'status-verify',
    init: 'status-init',
    error: 'status-error',
    expired: 'status-error'
};
let csrfToken = jQuery('meta[name="csrf-token"]').attr('content');

const INCIDENT_CODE_TEXT_TIMEOUT_EXPIRATION = 'TIMEOUT_EXPIRATION';

let iFinish = (Date.now() + 45000);
let strServiceVersion = 'PayInstore_v1';
let iStartProgressPerSecond = null;

let errorBlock = jQuery('.startpin-action-error');
let actionBlock = jQuery('.startpin-action');
let paymentProgressBlock = jQuery('#startpin-payment-progress');
let progressBar = jQuery('#progress');
let confirmBox = jQuery('#confirmBox');
let startPayment = jQuery('#start-payment');
let cancelPayment = jQuery('#cancel-payment');
let cancelTransactionButton = jQuery('#cancelTransactionButton');
let doEmailTicket = jQuery('input[name=doEmailTicket]');
let email = jQuery('input[name=email]');
let language = jQuery('select[name=language]');
let btnConfirmPayment = jQuery('button[name=btnConfirmPayment]');
let paymentTypeScanTab = jQuery('#payment-type-scan-tab');
let paymentTypeQrTab = jQuery('#payment-type-qr-tab');
let paymentTypePinTab = jQuery('#payment-type-pin-tab');
let scanLoadingBar = jQuery('#load-scan .progress-bar');
let scanLoadingTitle = jQuery('#load-scan .loading-title');
let scanLoadingBalance = jQuery('#balance .balance-amount');
let scanLoadingBlock = jQuery('#load-scan');

const API_TOKEN = jQuery('[name=api_token]');
const SERVICE_ID = jQuery('select[name=service_id]');
const TERMINAL_ID = jQuery('select[name=terminal_id]');

const QR_IMAGE_BLOCK = jQuery('#qr-image-block');
const STATUS_ICONS = jQuery('.status-icons');
const LOADING_GIF = jQuery('#loading_gif');
const CARD_NUMBER_BLOCK = jQuery('#cardnumber-block');
const CARD_NUMBER = jQuery('#cardnumber');
const PIN_CODE_BLOCK = jQuery('#pincode-block');
const PIN_CODE = jQuery('#pincode');

let lowerAnimationDuration = 150;
let middleAnimationDuration = 300;
let longestAnimationDuration = 450;
let qrCodeDisplayingTimeout = 600;
let displayingIconsTimeout = 1000;

const REQUEST_RESULT_SUCCESS = '1';
const REQUEST_RESULT_FAILED = '0';

const APPROVE_TRANSACTION_ENDPOINT = '/ajax/approve-transaction';
const DECLINE_TRANSACTION_ENDPOINT = '/ajax/decline-transaction';
const CANCEL_TRANSACTION_ENDPOINT = '/ajax/cancel-transaction';

const PIN_STATUS_CODE_START = 'start';
const PIN_STATUS_CODE_INIT = 'init';
const PIN_STATUS_CODE_APPROVED = 'approved';
const PIN_STATUS_CODE_DECLINED = 'declined';
const PIN_STATUS_CODE_EXPIRED = 'expired';
const PIN_STATUS_CODE_CANCELLED = 'cancelled';
const PIN_STATUS_CODE_ERROR = 'error';
const PIN_STATUS_CODE_VERIFY = 'verify';

const QR_STATUS_CODE_INIT = 'INIT';
const QR_STATUS_CODE_SCANNED = 'SCANNED';
const QR_STATUS_CODE_CONFIRMED = 'CONFIRMED';
const QR_STATUS_CODE_VERIFY = 'VERIFY';
const QR_STATUS_CODE_PAID = 'PAID';
const QR_STATUS_CODE_CANCELLED = 'CANCELLED';
const QR_STATUS_CODE_EXPIRED = 'EXPIRED';

const BARCODE_STATUS_CODE_PENDING = 'PENDING';
const BARCODE_STATUS_CODE_VERIFY = 'VERIFY';
const BARCODE_STATUS_CODE_CANCEL = 'CANCEL';
const BARCODE_STATUS_CODE_EXPIRED = 'EXPIRED';
const BARCODE_STATUS_CODE_PAID = 'PAID';
const BARCODE_STATUS_CODES = {
    '-90': BARCODE_STATUS_CODE_CANCEL,
    '-80': BARCODE_STATUS_CODE_EXPIRED,
    '-60': BARCODE_STATUS_CODE_CANCEL,
    '20': BARCODE_STATUS_CODE_PENDING,
    '25': BARCODE_STATUS_CODE_PENDING,
    '50': BARCODE_STATUS_CODE_PENDING,
    '85': BARCODE_STATUS_CODE_VERIFY,
    '90': BARCODE_STATUS_CODE_PENDING,
    '100': BARCODE_STATUS_CODE_PAID,
};

const CARD_NUMBER_TYPE_ALIPAY = 'alipay';
const CARD_NUMBER_TYPE_WECHAT = 'wechat';
const CARD_NUMBER_TYPE_GIFT = 'gift';

const QR_VERIFY_BUTTONS = jQuery('#qr-verify-buttons');
const PIN_VERIFY_BUTTONS = jQuery('#pin-verify-buttons');
const SCAN_VERIFY_BUTTONS = jQuery('#scan-verify-buttons');
const QR_PAYMENT_APPROVE = jQuery('#qr-payment-approve');
const QR_PAYMENT_DECLINE = jQuery('#qr-payment-decline');
const PIN_PAYMENT_APPROVE = jQuery('#pin-payment-approve');
const PIN_PAYMENT_DECLINE = jQuery('#pin-payment-decline');
const SCAN_PAYMENT_APPROVE = jQuery('#scan-payment-approve');
const SCAN_PAYMENT_DECLINE = jQuery('#scan-payment-decline');

if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

jQuery.fn.myStripTags = function () {
    let self = jQuery(this).val();
    // controleer of er een value bekend is
    if (typeof self !== 'undefined' && self !== null && self.length > 0) {
        jQuery(this).val(self.replace(/(<([^>]+)>)/ig, ""));
    }

    return this;
};

jQuery(document).ready(function () {
    // Triggering starting of transaction
    setTimeout(function() {
        let query = window.location.search.substring(1);
        let queryStringArray = parseQueryString(query);

        let isStartAuto = 'start' in queryStringArray && queryStringArray.start === 'auto';
        let isActiveTabPIN = typeof Cookies.get('activetab') !== 'undefined' ? Cookies.get('activetab') === 'pin' : true;
        let isTabPin = 'tab' in queryStringArray && queryStringArray.tab === 'pin' && isActiveTabPIN;
        if (isStartAuto && isTabPin) {
            jQuery(startPayment).trigger('click');
        }
    },10);

    /**
     * New transaction button
     */
    cancelPayment.on('click', function (event) {
        event.preventDefault();
        if (cancelPayment.attr('disabled')) {
            return false;
        }

        // check if button is active
        if (cancelPayment.hasClass('grey')) {
            return false;
        }

        let transactionType = jQuery(this).data('transactionType');
        errorBlock.hide();
        confirmBox.hide();
        resetConfirmationBlock();
        if (transactionType == 'pin') {
            cancelTransaction();
        } else if (transactionType == 'scan') {
            cancelBarcodePayment();
        } else if (transactionType == 'qr') {
            cancelQRPayment();
        }

        enableStartAndDisableCancelButtons();
        return false;
    });

    paymentTypeScanTab.on('click', function () {
        if (jQuery(this).attr('disabled')) {
            return false;
        }
        Cookies.set('activetab', 'scan');
        resetTabsContents();
        jQuery('.row-terminal_id').hide();
        startPayment.data('transactionType', 'scan');
        cancelPayment.data('transactionType', 'scan');
        getInputVars(function () {
            validateInputVars(function () {})
        });
    });

    paymentTypeQrTab.on('click', function () {
        if (jQuery(this).attr('disabled')) {
            return false;
        }
        Cookies.set('activetab', 'qr');
        resetTabsContents();
        jQuery('.row-terminal_id').hide();
        startPayment.data('transactionType', 'qr');
        cancelPayment.data('transactionType', 'qr');
        getInputVars(function () {
            validateInputVars(function () {})
        });
    });

    paymentTypePinTab.on('click', function () {
        if (jQuery(this).attr('disabled')) {
            return false;
        }
        Cookies.set('activetab', 'pin');
        resetTabsContents();
        startPayment.data('transactionType', 'pin');
        cancelPayment.data('transactionType', 'pin');
        getInputVars(function () {
            validateInputVars(function () {})
        });
        if (boolToggle) {
            jQuery('.row-terminal_id').show();
        }
    });

    /**
     * Start transaction button
     */
    startPayment.on('click', function () {
        if (startPayment.attr('disabled')) {
            return false;
        }

        // check if button is active
        if (startPayment.hasClass('grey')) {
            return false;
        }

        let transactionType = jQuery(this).data('transactionType');
        errorBlock.hide();
        confirmBox.hide();
        resetConfirmationBlock();

        if (transactionType == 'pin') {
            getInputVars(function () {
                validateInputVars(function () {
                    startPinTransaction();
                });
            });
        } else if (transactionType == 'scan') {
            getInputVars(function () {
                validateInputVars(function () {
                    validateScanInputVars(function () {
                        startScanPayment();
                    });
                });
            });
        } else if (transactionType == 'qr') {
            getInputVars(function () {
                validateInputVars(function () {
                    resetQR();
                    generateQrCode();
                });
            });
        }

        return false;
    });

    /**
     * An amount has been entered
     */
    jQuery('input[name="amount"]').blur(function () {

        // replace decimal point by comma and round by 2
        var val = this.value;
        val = val.replace(",", ".");

        var amount = parseFloat(val).toFixed(2);

        // check amount
        jQuery("input[name='amount']").val(isNaN(amount) ? '' : amount.replace(".", ","));
    });

    /**
     * Api field changed
     */
    jQuery('body').on('change', '[name=api_token]', function () {

        strApiToken = API_TOKEN.myStripTags().val();
        // get services
        getServices();

        // get terminals
        getTerminals();
    });

    jQuery('body').on('change', '[name=doEmailTicket]', function () {
        if (jQuery(this).is(':checked')) {
            enableConfirmationInputs();
        } else {
            disableConfirmationInputs();
        }
    });

    jQuery('body').on('change', '#startpin-form-checkboxSwitch2:checkbox', function () {
        if (jQuery(this).is(':checked')) {
            getInputVars(function () {
                let isValid = validateInputVars(function () {
                    return saveFormData();
                });
                if (!isValid) {
                    if (boolStaticFields) {
                        jQuery('#startpin-form-checkboxSwitch2').prop('checked', false);
                    } else {
                        jQuery('#startpin-form-checkboxSwitch2').prop('checked', true);
                    }
                } else {
                    if (paymentProgressBlock.is(':hidden')) {
                        errorBlock.hide();
                        actionBlock.show();
                    }
                }
            });
        } else {
            getInputVars(function () {
                clearFormData();
            });
        }
    });

    /**
     * Toggle button show all fields clicked
     */
    jQuery('body').on('click', '#startpin-form-checkboxSwitch', function () {
        getInputVars(function () {
            if (boolStaticFields) {
                saveFormData();
            }
        });

        jQuery("input[name=amount]").parents('div.row-amount')
            .toggleClass('col-xs-3').toggleClass('col-xs-6')
            .toggleClass('col-md-6').toggleClass('col-md-12')
            .toggleClass('mobile-half-width').toggleClass('mobile-full-width');

        if (boolToggle) {
            jQuery('.row-api_token').show(0);
            jQuery('.row-service_id').show(0);
            if (isTransactionTypePIN()) {
                jQuery('.row-terminal_id').show(0);
            }
            jQuery('.row-static-amount').show(0);
            jQuery('#language-change-block').show(0);
        } else {
            jQuery('.row-api_token').hide(0);
            jQuery('.row-service_id').hide(0);
            if (isTransactionTypePIN()) {
                jQuery('.row-terminal_id').hide(0);
            }
            jQuery('.row-static-amount').hide(0);
            jQuery('#language-change-block').hide(0);
        }
    });

    jQuery('body').on('keyup', '#cardnumber', function () {
        scanStartAllow = false;
        getInputVars(function () {
            validateScanInputVars(function () {
                let image = '';
                hidePINCodeBlock();
                jQuery('#load-scan .amount-count').text(getAmount());
                jQuery('#load-scan .loading-text').text(strDescription);
                jQuery('.balance-block').hide();
                if (checkScanCard()) {
                    jQuery('#balance .balance-amount').text('');
                    cardNumberType = CARD_NUMBER_TYPE_GIFT;
                    image = imagePattern.replace('{image_code}', scanCardImageCode);
                    jQuery('.image-block').show();
                    checkScanTransaction();
                } else if (checkAliPay()) {
                    jQuery('#balance .balance-amount').text('');
                    cardNumberType = CARD_NUMBER_TYPE_ALIPAY;
                    image = imagePattern.replace('{image_code}', aliPayImageCode);
                    jQuery('.image-block').show();
                } else if (checkWeChat()) {
                    jQuery('#balance .balance-amount').text('');
                    cardNumberType = CARD_NUMBER_TYPE_WECHAT;
                    image = imagePattern.replace('{image_code}', weChatImageCode);
                    jQuery('.image-block').show();
                } else {
                    jQuery('#balance .balance-amount').text('');
                    cardNumberType = null;
                    let errorMessage = jQuery('.startpin-cardnumber-validation-error').html();
                    showErrorMessage(errorMessage);
                    jQuery('.image-block').hide();
                }
                clearInterval(transactionInterval);
                jQuery('.image-block img').show().attr('src', image);
            });
        });
    });

    jQuery('.select-language').on('click', function (e) {
        e.preventDefault();
        let selectedLang = jQuery(this).data('lang');
        Cookies.set('lang', selectedLang);
        jQuery('form[name=startpin]').submit();
    });

    jQuery('#print-receipt-modal').on('shown.bs.modal', function () {
        let base64 = jQuery('#print-receipt').data('base64');
        printJS({
            printable: base64,
            type: 'pdf',
            base64: true,
            fallbackPrintable: base64,
        });
    });

    QR_PAYMENT_APPROVE.on('click', function (event) {
        event.preventDefault();
        approveQRPayment();
    });

    QR_PAYMENT_DECLINE.on('click', function (event) {
        event.preventDefault();
        declineQRPayment();
    });

    PIN_PAYMENT_APPROVE.on('click', function (event) {
        event.preventDefault();
        approvePINPayment();
    });

    PIN_PAYMENT_DECLINE.on('click', function (event) {
        event.preventDefault();
        declinePINPayment();
    });

    SCAN_PAYMENT_APPROVE.on('click', function (event) {
        event.preventDefault();
        approveBarcodePayment();
    });

    SCAN_PAYMENT_DECLINE.on('click', function (event) {
        event.preventDefault();
        declineBarcodePayment();
    });

    PIN_CODE.on('blur', function () {
        if (!validatePincode()) {
            PIN_CODE_BLOCK.addClass('has-error');
            errorMessage = jQuery('.startpin-scan-pincode-error').html();
            showErrorMessage(errorMessage);
        }
    })

    PIN_CODE.on('focusin', function () {
        PIN_CODE_BLOCK.removeClass('has-error');
        errorBlock.hide();
        actionBlock.show();
    })

    jQuery('select.select2').select2({
        theme: "bootstrap"
    });
});

function resetTabsContents() {
    resetQR();
    setClearBarcodeTab();
}

function setClearBarcodeTab() {
    resetScanProgress();
    CARD_NUMBER.val('');
    hidePINCodeBlock();
    jQuery('.balance-block').hide();
    jQuery('#payment-type-scan .image-block').hide();
}

function resetQR() {
    jQuery('#load-qr .loading-title')
        .removeClass('alert-success alert-danger alert-warning')
        .addClass('alert-info');
    jQuery('#load-qr .progress .progress-bar')
        .removeClass('bg-strong-success bg-strong-danger bg-strong-warning')
        .clearQueue()
        .stop();
    jQuery('#paid, #app_icon, #loading_gif, #loading_block .status-icons').css({opacity: 0, display: 'none'});
    jQuery('#qr_code').attr('src', '').css({opacity: 1});
    QR_IMAGE_BLOCK.removeClass('paid_config').hide();
    jQuery('#load-qr').hide();
    QR_VERIFY_BUTTONS.hide();
    clearInterval(transactionInterval);
}

function generateQrCode() {
    let objParameters = {
        amount: iAmount/100,
        api_token: strApiToken,
        service_id: strServiceId,
        description: strDescription,
        orderNumber: strOrderNumber
    };

    QR_IMAGE_BLOCK.show();
    showStatusIcons();
    showLoadingGif();
    disableStartButton();

    jQuery.ajax({
        cache: false,
        url: '/ajax/getQRCode',
        data: objParameters,
        headers: {'X-CSRF-TOKEN': csrfToken},
        type: "post",
        dataType: "json",
        success: function (data) {
            if (data.generated_qr_url) {
                if (data.hasOwnProperty('transactionId')) {
                    iTransactionId = data.transactionId;
                }
                if (data.hasOwnProperty('uuid')) {
                    transactionUUID = data.uuid;
                }
                jQuery('#qr_code').attr('src', data.generated_qr_url)
                    .one('load', function() {
                        enableCancelButton();
                        hideStatusIcons();
                        hideLoadingGif();
                        startQRTransaction();
                        getTransactionStatus(transactionUUID);
                    });
            } else {
                showErrorMessage(data.error);
            }
        }
    });
}

function disableStartAndEnableCancelButtons() {
    disableStartButton();
    enableCancelButton();
}

function enableStartAndDisableCancelButtons() {
    startPayment.attr('disabled', false).removeClass('grey').addClass('pink');
    cancelPayment.removeClass('btn-danger').addClass('grey');
    jQuery('#startpin-form .nav-tabs li a').attr('disabled', false);
}

function disableStartButton() {
    startPayment.attr('disabled', 'disabled').removeClass('pink').addClass('grey');
    jQuery('#startpin-form .nav-tabs li a').attr('disabled', true);
}

function enableCancelButton() {
    cancelPayment.removeClass('grey').addClass('btn-danger');
}

function disableCardNumberField() {
    CARD_NUMBER.attr('disabled', true);
}

function enableCardNumberField() {
    CARD_NUMBER.attr('disabled', false);
}

function disablePINCodeField() {
    PIN_CODE.attr('disabled', true);
}

function enablePINCodeField() {
    PIN_CODE.attr('disabled', false);
}

function startQRTransaction() {
    defaultQRLoadingBody();
    startQRLoading();
    jQuery('#load-qr').show();
}

function checkScanCard() {
    let cardCheck = cardNumber.substring(0, 4);
    if (cardNumber.length == 19 && cardCheck == 6064) {
        return true;
    }

    return false;
}

function checkWeChat() {
    let cardCheck = cardNumber.substring(0, 2);
    if (cardNumber.length == 18 && cardCheck >= 10 && cardCheck <= 15) {
        return true;
    }

    return false;
}

function checkAliPay() {
    let cardCheck = cardNumber.substring(0, 2);
    if ((cardNumber.length >= 16 && cardNumber.length <= 24) && (cardCheck >= 26 && cardCheck <= 30)) {
        return true;
    }

    return false;
}

function startPinTransaction() {
    // set variables
    current_api = "start";

    if (boolStaticFields) {
        // set cookies
        saveFormData();
    }

    pay_getParameters(function (data) {

        pay_doApiCall(handleTransactionStart, data);
    });
}

function checkScanTransaction() {
    // set variables
    current_api = "scan";

    if (boolStaticFields) {
        // set cookies
        saveFormData();
    }

    pay_getParameters(function (data) {

        pay_doApiCall(handleScanTransaction, data);
    });
}

function startScanPayment() {
    // set variables
    current_api = "start-scan";

    if (boolStaticFields) {
        // set cookies
        saveFormData();
    }

    pay_getParameters(function (data) {
        startScanLoading();
        disableCardNumberField();
        if (cardNumberType === CARD_NUMBER_TYPE_GIFT) {
            disableStartButton();
            disablePINCodeField();
        } else {
            disableStartAndEnableCancelButtons();
        }
        pay_doApiCall(handleStartScanTransaction, data);
    });
}

function getAmount() {
    let amount = currency(iAmount / 100);
    amount = amount.toString().replace('.', ',');

    return amount;
}

function defaultQRLoadingBody() {
    let text = jQuery('.startpin-qr-pending-message').text();
    let amount = getAmount();
    jQuery('.loading-amount .amount-count').text(amount);
    jQuery('#load-qr .loading-text').text(strDescription);
    jQuery('#load-qr .loading-title').text(text).removeClass('alert-danger').removeClass('alert-success').addClass('alert-info');
    jQuery('#load-qr .progress-bar').removeClass('bg-strong-danger').removeClass('bg-strong-success').removeClass('bg-info');
}

function defaultLoadingBody() {
    let text = jQuery('.startpin-scan-pending-message').text();
    let amount = getAmount();
    jQuery('.loading-amount .amount-count').text(amount);
    jQuery('#load-scan .loading-text').text(strDescription);
    scanLoadingTitle.text(text).removeClass('alert-danger').removeClass('alert-success').addClass('alert-info');
    scanLoadingBar.removeClass('bg-strong-danger').removeClass('bg-strong-success').removeClass('bg-info');
}

function handleStartScanTransaction(data) {
    if (typeof data.request !== 'undefined') {
        if (data.request.result === REQUEST_RESULT_FAILED) {
            const message = data.request.errorMessage;
            showErrorMessage(message);
            setScanErrorBar(message);
            enableStartAndDisableCancelButtons();

            return;
        }
    }

    if (data.hasOwnProperty('transaction') && data.transaction.hasOwnProperty('transactionId')) {
        iTransactionId = data.transaction.transactionId;
    }
    defaultLoadingBody();
    if (cardNumberType === CARD_NUMBER_TYPE_ALIPAY || cardNumberType === CARD_NUMBER_TYPE_WECHAT) {
        activateScanLoading(data);
    } else if (cardNumberType === CARD_NUMBER_TYPE_GIFT) {
        if (true === scanStartAllow) {
            scanLoadingBlock.removeClass('hidden');
            startGiftLoading(data);
        }
    }
}

function validateAfterPinCheck(pin) {
    if (pin == 1) {
        scanStartAllow = true;
    } else {
        scanStartAllow = false;
    }
}

function handleScanTransaction(data) {
    if (typeof data.request !== 'undefined') {
        if (data.request.result === REQUEST_RESULT_FAILED) {
            showErrorMessage(data.request.errorMessage);

            return;
        }
    }

    let withPin = data.card.withPin;
    let amount = getAmount();

    validateAfterPinCheck(withPin);
    balance = currency(data.card.balance / 100);
    jQuery('.balance-block').show();
    scanLoadingBalance.text(balance.toString().replace('.', ','));

    if (balance > 0 && balance >= amount) {
        if (true === scanStartAllow) {
            PIN_CODE_BLOCK.removeClass('has-error')
                .show();
        } else {
            hidePINCodeBlock();
        }
    } else {
        let errorMessageIvalidBalance = jQuery('.startpin-scan-invalid-balance-message').html();
        showErrorMessage(errorMessageIvalidBalance);
    }
}

function hidePINCodeBlock() {
    PIN_CODE_BLOCK.hide();
    PIN_CODE.val('');
}

function getServices() {

    // we need to have at least an api token
    if (true === validateApiToken()) {
        // set variables
        current_api = "service";

        SERVICE_ID.attr('disabled', false);

        pay_getParameters(function (data) {

            pay_doApiCall(populateServices, data);
        });
    }
    else {
        resetServiceHtml();
    }
}

/**
 * Get available terminals
 *
 * @returns {unresolved}
 */
function getTerminals() {
    // we need to have at least an api token
    if (true === validateApiToken()) {
        // set variables
        current_api = "terminal";

        TERMINAL_ID.attr('disabled', false);

        pay_getParameters(function (data) {

            pay_doApiCall(populateTerminals, data);
        });
    } else {
        resetTerminalHtml();
    }

    return;
}

function isTransactionTypePIN() {
    return startPayment.data('transactionType') === 'pin';
}

/**
 * Reset service selectbox
 *
 * @returns {undefined}
 */
function resetServiceHtml() {
    SERVICE_ID
        .attr('disabled', true)
        .html('<option></option>');
}

/**
 * Reset terminal selectbox
 *
 * @returns {undefined}
 */
function resetTerminalHtml() {
    TERMINAL_ID
        .attr('disabled', true)
        .html('<option></option>');
}

/**
 * Populate service selectbox
 *
 * @param {type} data
 * @returns {undefined}
 */
function populateServices(data) {
    if (typeof data.request != 'undefined' && data.request.result == '1') {
        var strOption = '';

        $.each(data.services, function (key, value) {

            strOption += '<option value="' + value.id + '">' + value.id + ' - ' + value.name + '</option>';
        });

        SERVICE_ID.html(strOption);
    }
    else {
        resetTerminalHtml();

        errorMessage = jQuery('.startpin-error-could-not-find-service').html();
        showErrorMessage(errorMessage);
    }
}

/**
 * Populate terminal selectbox
 *
 * @param {type} data
 * @returns {undefined}
 */
function populateTerminals(data) {
    if (typeof data.request != 'undefined' && data.request.result == '1') {
        var strOption = '';

        $.each(data.terminals, function (key, value) {

            strOption += '<option value="' + value.id + '">' + value.id + ' - ' + value.name + '</option>';
        });

        TERMINAL_ID.html(strOption);
        paymentTypePinTab.attr('disabled', false);
    } else {
        resetTerminalHtml();
        paymentTypePinTab.attr('disabled', true);

        if (isTransactionTypePIN()) {
            errorMessage = jQuery('.startpin-error-could-not-find-terminal').html();
            showErrorMessage(errorMessage);
        }
    }
}

/**
 * Validate input values
 *
 * @returns {Boolean}
 */
function validateScanInputVars(callback) {
    if (false === validateCardNumber()) {
        CARD_NUMBER_BLOCK.addClass('has-error');
        errorMessage = jQuery('.startpin-cardnumber-validation-error').html();
        showErrorMessage(errorMessage);
        hidePINCodeBlock();
        scanLoadingBlock.addClass('hidden');
        jQuery('.image-block img').hide().attr('src', '');
        jQuery('.balance-block').hide();
        scanLoadingBalance.text('');

        return false;
    } else {
        CARD_NUMBER_BLOCK.removeClass('has-error');
    }

    if (scanStartAllow && !validatePincode()) {
        PIN_CODE_BLOCK.addClass('has-error');
        errorMessage = jQuery('.startpin-scan-pincode-error').html();
        showErrorMessage(errorMessage);
        scanLoadingBlock.addClass('hidden');

        return false;
    }

    errorBlock.hide();
    actionBlock.show();

    return callback();
}

/**
 * Validate input values
 *
 * @returns {Boolean}
 */
function validateInputVars(callback) {
    if (false === validateAmount()) {
        errorMessage = jQuery('.startpin-amount-validation-error').html();
        showErrorMessage(errorMessage);

        return false;
    }

    if (false === validateDescription()) {
        errorMessage = jQuery('.startpin-description-validation-error').html();
        showErrorMessage(errorMessage);

        return false;
    }

    if (false === validateOrderNumber()) {
        errorMessage = jQuery('.startpin-order_number-validation-error').html();
        showErrorMessage(errorMessage);

        return false;
    }

    if (false === validateApiToken()) {
        errorMessage = jQuery('.startpin-api-token-validation-error').html();
        showErrorMessage(errorMessage);

        return false;
    }

    if (false === validateServiceId()) {
        errorMessage = jQuery('.startpin-service-id-validation-error').html();
        showErrorMessage(errorMessage);

        return false;
    }

    if (false === validateTerminalId() && isTransactionTypePIN()) {
        if (strTerminalId === null) {
            errorMessage = jQuery('.startpin-terminal-id-is-empty-error').html();
        } else {
            errorMessage = jQuery('.startpin-terminal-id-validation-error').html();
        }
        showErrorMessage(errorMessage);

        return false;
    }

    errorBlock.hide();
    actionBlock.show();

    return callback();
}

/**
 * Validate amount
 *
 * @returns {Boolean}
 */
function validateCardNumber() {
    let isCardNumberValid = checkScanCard() || checkWeChat() || checkAliPay();

    return isCardNumberValid && !isNaN(iAmount);
}

/**
 * Validate amount
 *
 * @returns {Boolean}
 */
function validateAmount() {
    if (iAmount == 0 || isNaN(iAmount)) {
        return false;
    }

    return true;
}

/**
 * Validate amount
 *
 * @returns {Boolean}
 */
function validatePincode() {
    let pincodeVal = PIN_CODE.val();

    return !(pincodeVal.length !== 6 || isNaN(iAmount));
}

/**
 * Validate Service Id
 *
 * @returns {Boolean}
 */
function validateServiceId() {
    if (strServiceId.length == 12) {
        return true;
    }

    return false;
}

/**
 * Validate API token
 *
 * @returns {Boolean}
 */
function validateApiToken() {
    if (strApiToken.length == 40) {
        return true;
    }

    return false;
}

/**
 * Validate terminal ID
 *
 * @returns {Boolean}
 */
function validateTerminalId() {
    if (strTerminalId !== null && strTerminalId.length == 12) {
        return true;
    }

    return false;
}

/**
 * Validate description
 *
 * @returns {Boolean}
 */
function validateDescription() {
    if (strDescription.length > 0) {
        return true;
    }

    return false;
}

/**
 * Validate Order number
 *
 * @returns {Boolean}
 */
function validateOrderNumber() {
    if (strOrderNumber.length <= 16) {
        return true;
    }

    return false;
}

/**
 * Get input values
 *
 * @param {type} callback
 * @returns {unresolved}
 */
function getInputVars(callback) {
    resetVars();

    iAmount = doAmount();
    strServiceId = jQuery("[name=service_id]").myStripTags().val();
    cardNumber = CARD_NUMBER.myStripTags().val();
    strApiToken = API_TOKEN.myStripTags().val();
    if (PIN_CODE) {
        pinCode = PIN_CODE.myStripTags().val();
    }
    strTerminalId = jQuery("[name=terminal_id]").myStripTags().val();
    strDescription = jQuery("input[name=description]").myStripTags().val();
    strOrderNumber = jQuery("input[name=order_number]").myStripTags().val();
    strOrderNumber = escape(strOrderNumber.substring(0, 15));
    boolToggle = jQuery('#startpin-form-checkboxSwitch').is(':checked');
    boolStaticFields = jQuery('#startpin-form-checkboxSwitch2').is(':checked');

    return callback();
}

/**
 * Convert amount to be able to pass it to the Transaction start
 *
 * @returns {Number|doAmount.amount}
 */
function doAmount() {
    let amount = jQuery("[name=amount]").myStripTags().val();

    amount = amount.replace(",", ".");
    amount = parseFloat(amount).toFixed(2);
    amount = Math.round(amount * 100);

    return isNaN(amount) ? 0.0 : amount;
}

/**
 * Reset values
 *
 * @returns {undefined}
 */
function resetVars() {
    current_api = null;
    iAmount = null;
    strServiceId = null;
    strApiToken = null;
    iTransactionId = null;
    strTerminalId = null;
    cardNumber = null;
}

/**
 * Get the transaction id and start a pin transaction
 *
 * @param {type} data
 * @returns {undefined}
 */
function handleTransactionStart(data) {
    if (data.hasOwnProperty('transaction') && data.transaction.hasOwnProperty('transactionId')) {
        iTransactionId = data.transaction.transactionId;
    }
    current_api = "payment";

    if (iAmount > 0) {
        pay_getParameters(function (data) {
            pay_doApiCall(handleInstorePayment, data);
        });
    } else {
        handleInstorePayment(data);
    }
}

/**
 * Handle pin transaction
 *
 * @param {type} data
 * @returns {undefined}
 */
function handleInstorePayment(data) {
    if (typeof data.request !== 'undefined') {
        if (data.request.result == '0' || data.request.result == 'FALSE') {
            if (data.request.errorMessage == 'Terminal in use') {
                errorMessage = jQuery('.startpin-error-terminal-in-use').html();
                errorMessage = errorMessage.replace(/%s/g, strTerminalId);
                showErrorMessage(errorMessage);

                let standardTimer = 48;
                if (transactionTimer > 3) {
                    standardTimer = transactionTimer;
                }
                startTimer(standardTimer, jQuery('span#timer'));
            } else {
                showErrorMessage(data.request.errorMessage);
                enableStartAndDisableCancelButtons();
            }
        } else if (data.request.result == '1' || data.request.result == 'TRUE') {
            handlePaymentProgress(data);
        }
    } else {
        showErrorMessage(jQuery('.startpin-error-could-not-start-transaction').html());
        enableStartAndDisableCancelButtons();
    }

    return;
}

function handlePaymentProgress(data) {
    if (iAmount > 0) {
        strStatusApiUrl = data.transaction.statusUrl;
        strHash = data.transaction.terminalHash;
    } else {
        strStatusApiUrl = data.terminal.statusUrl;
        strHash = data.terminal.hash;
    }

    paymentTransactionId = data.transaction.transactionId;
    let replacedUrl = strStatusApiUrl.replace('/status?', '/cancel?');
    strCancelApiUrl = replacedUrl.substr(0, replacedUrl.indexOf('&'));
    if (typeof data.progress !== 'undefined' &&
      typeof data.progress.percentage_per_second !== 'undefined') {
        iStartProgressPerSecond = data.progress.percentage_per_second;
    } else {
        iStartProgressPerSecond = 2.222;
    }

    if (typeof data.transaction.state !== 'undefined') {
        strStatusPayment = data.transaction.state;
    } else {
        strStatusPayment = PIN_STATUS_CODE_INIT;
    }

    if (typeof data.terminal.state !== 'undefined') {
        strTerminalStatus = data.terminal.state;
    } else {
        strTerminalStatus = PIN_STATUS_CODE_START;
    }

    resetProgress();
    setProgress();
    getStatus(strHash, true);
    setStatusStyle(strStatusPayment);

    disableStartAndEnableCancelButtons();
    fillTransactionProgressBlock();

    actionBlock.hide();
    cancelTransactionButton.show();
    paymentProgressBlock.show();
}

/**
 * Setup API URLs
 *
 * @param {type} callback
 * @returns {undefined}
 */
function pay_getParameters(callback) {
    var objParameters = {};

    if (current_api === 'start') {
        objParameters = {
            token: strApiToken,
            serviceId: strServiceId,
            amount: iAmount,
            terminalId: strTerminalId,
        };

        if (strDescription.length > 0) {
            objParameters.description = escape(strDescription.substring(0, 31));
        }

        if (strOrderNumber.length > 0) {
            objParameters.orderNumber = strOrderNumber;
        }
    } else if (current_api === 'payment') {
        objParameters = {
            token: strApiToken,
            terminalId: strTerminalId,
            transactionId: iTransactionId
        };
    } else if (current_api === 'terminal') {
        objParameters = {
            token: strApiToken
        };
    } else if (current_api === 'service') {
        objParameters = {
            token: strApiToken
        };
    } else if (current_api === 'scan') {
        objParameters = {
            apiToken: strApiToken,
            cardNumber: cardNumber
        };
    } else if (current_api === 'start-scan') {
        objParameters = {
            apiToken: strApiToken,
            serviceId: strServiceId,
            amount: iAmount,
            pin: pinCode,
            description: strDescription,
            cardNumber: cardNumber,
            cardNumberType: cardNumberType
        };

      if (strOrderNumber.length > 0) {
        objParameters.orderNumber = strOrderNumber;
      }
    }

    callback(objParameters);
}


/**
 * Invoke Pay.nl API
 *
 * @param {type} handleData
 * @param {type} objParameters
 * @returns {undefined}\
 */
function pay_doApiCall(handleData, objParameters) {

    var strUrl = '';

    switch (current_api) {
        case 'start':
            strUrl = lang + '/ajax/doInstoreStart';
            break;
        case 'payment':
            strUrl = lang + '/ajax/doInstorePayment';
            break;
        case 'terminal':
            strUrl = lang + '/ajax/getInstoreTerminals';
            break;
        case 'service':
            strUrl = lang + '/ajax/getInstoreServices';
            break;
        case 'scan':
            strUrl = 'ajax/checkScannedCard';
            break;
        case 'start-scan':
            strUrl = 'ajax/startScannedCardTransaction';
            break;
        default:
            return;
    }
    strUrl = '/' + strUrl;

    $.ajax({
        url: strUrl,
        data: objParameters,
        headers: {'X-CSRF-TOKEN': csrfToken},
        type: "post",
        dataType: "json",
        cache: false,
        success: function (data) {
            if (data.status === 429) {
                showErrorMessage(data.message);
            } else {
                // call the function which handles the result
                return handleData(data);
            }
        }
    })
    .fail(function (failedResponse) {
        if (failedResponse.status === 403) {
            showErrorMessage(failedResponse.responseJSON.error_message);
        }
    });
}

function getTransactionStatus(uuid) {
    callQrStatusCheck(uuid);
    jQuery('#loading_block').data(QR_STATUS_CODE_INIT, false);
}

function qrShowLoadingAndIcon(paymentProfileIssuerId, paymentProfileId) {
    let image = imageQRPattern.replace('{image_code}', paymentProfileIssuerId);
    if (paymentProfileIssuerId == 0) {
        image = imageQRPaymentMethodPattern.replace('{image_code}', paymentProfileId);
    }

    jQuery('#app_icon').animate({opacity: 0}, lowerAnimationDuration)
      .hide(0);
    setTimeout(function () {
        jQuery('#app_icon').attr('src', image);
        showStatusIcons();
        showLoadingGif();
    }, lowerAnimationDuration);

    setTimeout(function () {
        LOADING_GIF.animate({opacity: 0}, lowerAnimationDuration);
        setTimeout(function () {
            LOADING_GIF.hide(0);
            jQuery('#app_icon').show(0)
              .animate({opacity: 1}, middleAnimationDuration);
        }, lowerAnimationDuration);
    }, displayingIconsTimeout);
}

function showStatusIcons() {
    STATUS_ICONS.show(0)
        .animate({opacity: 1}, lowerAnimationDuration);
}

function hideStatusIcons() {
    STATUS_ICONS.hide(0)
        .animate({opacity: 0}, lowerAnimationDuration);
}

function showLoadingGif() {
    LOADING_GIF.show(0)
        .animate({opacity: 1}, longestAnimationDuration);
}

function hideLoadingGif() {
    LOADING_GIF.hide(0)
        .animate({opacity: 0}, lowerAnimationDuration);
}

function qrShowPaid() {
    showStatusIcons();
    jQuery('#app_icon').animate({
        opacity: 0
    }, lowerAnimationDuration);
    setTimeout(function () {
        jQuery('#app_icon').hide(0);
        QR_IMAGE_BLOCK.addClass('paid_config');
        jQuery('#paid').show(0);
        jQuery('#paid').animate({
            opacity: 1
        }, middleAnimationDuration);
        jQuery('#paid').attr('src','/img/checkmark.gif');
    }, lowerAnimationDuration);
    setTimeout(function () {
        blurQRCode();
    }, qrCodeDisplayingTimeout);
}

function blurQRCode() {
    jQuery('#qr_code').animate({
        opacity: 0.2
    }, middleAnimationDuration);
}

function blurStatusIcons() {
    STATUS_ICONS.animate({
        opacity: 0.5
    }, middleAnimationDuration);
}

function callQrStatusCheck(uuid) {
    let strUrl = SAFE_PAY_NL_API_STATUS + uuid;

    jQuery.ajax({
        url: strUrl,
        type: "post",
        dataType: "json",
        cache: false,
        success: function (data) {
            clearInterval(transactionInterval);
            if (data.result) {
                if (data.statusCode === QR_STATUS_CODE_INIT) {
                    jQuery('#loading_block')
                        .data(QR_STATUS_CODE_SCANNED, false)
                        .data(QR_STATUS_CODE_CONFIRMED, false);
                    let timeout = 0;
                    if (true === jQuery('#loading_block').data(QR_STATUS_CODE_INIT)) {
                        timeout = 5000;
                    } else {
                        timeout = 10000;
                    }

                    clearInterval(transactionInterval);
                    transactionInterval = setInterval(function () {
                        callQrStatusCheck(uuid);
                    }, timeout);
                    jQuery('#loading_block').data(QR_STATUS_CODE_INIT, true);
                } else if (data.statusCode === QR_STATUS_CODE_SCANNED) {
                    let scannedMessage = jQuery('.startpin-qr-scanned-status').html();
                    clearInterval(transactionInterval);
                    if (false === jQuery('#loading_block').data(QR_STATUS_CODE_SCANNED)) {
                        qrShowLoadingAndIcon(data.paymentProfileIssuerId, data.paymentProfileId);
                    }
                    jQuery('#load-qr .loading-title').text(scannedMessage);
                    jQuery('#loading_block').data(QR_STATUS_CODE_SCANNED, true);
                    transactionInterval = setInterval(function () {
                        callQrStatusCheck(uuid)
                    }, 2000);
                } else if (data.statusCode === QR_STATUS_CODE_CONFIRMED) {
                    let confirmedMessage = jQuery('.startpin-qr-confirmed-status').html();
                    clearInterval(transactionInterval);
                    if (false === jQuery('#loading_block').data(QR_STATUS_CODE_CONFIRMED)) {
                        qrShowLoadingAndIcon(data.paymentProfileIssuerId, data.paymentProfileId);
                    }
                    jQuery('#load-qr .loading-title').text(confirmedMessage);
                    jQuery('#loading_block').data(QR_STATUS_CODE_CONFIRMED, true);
                    transactionInterval = setInterval(function () {
                        callQrStatusCheck(uuid)
                    }, 2000);
                } else if (data.statusCode === QR_STATUS_CODE_VERIFY) {
                    clearInterval(transactionInterval);
                    setQrLoadingBarStatusVerified();
                    QR_VERIFY_BUTTONS.show();
                    transactionInterval = setInterval(function () {
                        callQrStatusCheck(uuid)
                    }, 2000);
                } else if (data.statusCode === QR_STATUS_CODE_PAID) {
                    jQuery('#load-qr .progress-bar').clearQueue().stop();
                    QR_VERIFY_BUTTONS.hide();
                    qrShowPaid();
                    clearInterval(transactionInterval);
                    setQrLoadingBarStatusPaid();
                    enableStartAndDisableCancelButtons();
                } else if (data.statusCode === QR_STATUS_CODE_CANCELLED) {
                    let message = jQuery('.startpin-qr-cancel-message').text();
                    setQrLoadingBarError(message);
                    finishQRPaymentProcessing();
                } else if (data.statusCode === QR_STATUS_CODE_EXPIRED) {
                    let message = jQuery('.startpin-transaction-status-expired').text();
                    setQrLoadingBarError(message);
                    finishQRPaymentProcessing();
                } else {
                    clearInterval(transactionInterval);
                }
            }
        }
    })
        .fail(function (failedResponse) {
            showErrorMessage(failedResponse.responseJSON.error_message);
        });
}

function approvePINPayment() {
    let strUrl = lang + APPROVE_TRANSACTION_ENDPOINT;

    jQuery.ajax({
        url: strUrl,
        type: "post",
        headers: {'X-CSRF-TOKEN': csrfToken},
        data: getPaymentParameters(),
        dataType: "json",
        cache: false,
        success: function (data) {
            if (typeof data.request !== 'undefined') {
                if (data.request.result === REQUEST_RESULT_FAILED) {
                    processFailedPINTransaction();
                }
                PIN_VERIFY_BUTTONS.hide();
            }
        }
    })
        .fail(function (failedResponse) {
            showErrorMessage(failedResponse.responseJSON.error_message);
        });
}

function declinePINPayment() {
    let strUrl = lang + DECLINE_TRANSACTION_ENDPOINT;

    jQuery.ajax({
        url: strUrl,
        type: "post",
        data: getPaymentParameters(),
        headers: {'X-CSRF-TOKEN': csrfToken},
        dataType: "json",
        cache: false,
        success: function (data) {
            if (typeof data.request !== 'undefined') {
                if (data.request.result === REQUEST_RESULT_FAILED) {
                    processFailedPINTransaction();
                }
                PIN_VERIFY_BUTTONS.hide();
            }
        }
    })
        .fail(function (failedResponse) {
            showErrorMessage(failedResponse.responseJSON.error_message);
        });
}

function processFailedPINTransaction() {
    clearTimeout(rStatusTimeout);
    strTerminalStatus = PIN_STATUS_CODE_ERROR;
    setStatusStyle(PIN_STATUS_CODE_ERROR);
    enableStartAndDisableCancelButtons();
}

function approveQRPayment() {
    let strUrl = lang + APPROVE_TRANSACTION_ENDPOINT;

    jQuery.ajax({
        url: strUrl,
        type: "post",
        data: getPaymentParameters(),
        headers: {'X-CSRF-TOKEN': csrfToken},
        dataType: "json",
        cache: false,
        success: function (data) {
            if (typeof data.request !== 'undefined') {
                if (data.request.result === REQUEST_RESULT_SUCCESS) {
                    clearInterval(transactionInterval);
                    callQrStatusCheck(transactionUUID);
                } else if (data.request.result === REQUEST_RESULT_FAILED) {
                    if (typeof data.request.errorMessage !== 'undefined') {
                        setQrLoadingBarError(data.request.errorMessage);
                    }
                }
                finishQRPaymentProcessing();
            }
        }
    })
        .fail(function (failedResponse) {
            showErrorMessage(failedResponse.responseJSON.error_message);
        });
}

function declineQRPayment() {
    let strUrl = lang + DECLINE_TRANSACTION_ENDPOINT;

    jQuery.ajax({
        url: strUrl,
        type: "post",
        data: getPaymentParameters(),
        headers: {'X-CSRF-TOKEN': csrfToken},
        dataType: "json",
        cache: false,
        success: function (data) {
            if (typeof data.request !== 'undefined') {
                let message = '';
                if (data.request.result === REQUEST_RESULT_SUCCESS) {
                    message = data.message;
                } else if (data.request.result === REQUEST_RESULT_FAILED) {
                    if (typeof data.request.errorMessage !== 'undefined') {
                        message = data.request.errorMessage;
                    }
                }
                setQrLoadingBarError(message);
                finishQRPaymentProcessing();
            }
        }
    })
        .fail(function (failedResponse) {
            showErrorMessage(failedResponse.responseJSON.error_message);
        });
}

function cancelQRPayment() {
    let strUrl = lang + CANCEL_TRANSACTION_ENDPOINT;

    jQuery.ajax({
        url: strUrl,
        type: "post",
        data: getPaymentParameters(),
        headers: {'X-CSRF-TOKEN': csrfToken},
        dataType: "json",
        cache: false,
        success: function (data) {
            if (typeof data.request !== 'undefined') {
                let message = '';
                if (data.request.result === REQUEST_RESULT_SUCCESS) {
                    message = data.message;
                } else if (data.request.result === REQUEST_RESULT_FAILED) {
                    if (typeof data.request.errorMessage !== 'undefined') {
                        message = data.request.errorMessage;
                    }
                }
                setQrLoadingBarError(message);
                finishQRPaymentProcessing();
            }
        }
    })
        .fail(function (failedResponse) {
            showErrorMessage(failedResponse.responseJSON.error_message);
        });
}

function approveBarcodePayment() {
    let strUrl = lang + APPROVE_TRANSACTION_ENDPOINT;
    clearInterval(transactionInterval);

    jQuery.ajax({
        url: strUrl,
        type: "post",
        data: getPaymentParameters(),
        headers: {'X-CSRF-TOKEN': csrfToken},
        dataType: "json",
        cache: false,
        success: function (data) {
            if (typeof data.request !== 'undefined') {
                if (data.request.result === REQUEST_RESULT_SUCCESS) {
                    statusCheck(iTransactionId);
                } else if (data.request.result === REQUEST_RESULT_FAILED) {
                    if (typeof data.request.errorMessage !== 'undefined') {
                        setScanErrorBar(data.request.errorMessage);
                    }
                }
                enableCardNumberField();
            }
        }
    })
        .fail(function (failedResponse) {
            showErrorMessage(failedResponse.responseJSON.error_message);
        });
}

function declineBarcodePayment(){
    let strUrl = lang + DECLINE_TRANSACTION_ENDPOINT;

    jQuery.ajax({
        url: strUrl,
        type: "post",
        data: getPaymentParameters(),
        headers: {'X-CSRF-TOKEN': csrfToken},
        dataType: "json",
        cache: false,
        success: function (data) {
            if (typeof data.request !== 'undefined') {
                let message = '';
                if (data.request.result === REQUEST_RESULT_SUCCESS) {
                    message = data.message;
                } else if (data.request.result === REQUEST_RESULT_FAILED) {
                    if (typeof data.request.errorMessage !== 'undefined') {
                        message = data.request.errorMessage;
                    }
                }
                setScanErrorBar(message);
                enableCardNumberField();
                clearInterval(transactionInterval);
            }
        }
    })
        .fail(function (failedResponse) {
            showErrorMessage(failedResponse.responseJSON.error_message);
        });
}

function cancelBarcodePayment() {
    let strUrl = lang + CANCEL_TRANSACTION_ENDPOINT;

    jQuery.ajax({
        url: strUrl,
        type: "post",
        data: getPaymentParameters(),
        headers: {'X-CSRF-TOKEN': csrfToken},
        dataType: "json",
        cache: false,
        success: function (data) {
            if (typeof data.request !== 'undefined') {
                if (data.request.result === REQUEST_RESULT_SUCCESS) {
                    setScanCancelBar();
                } else if (data.request.result === REQUEST_RESULT_FAILED) {
                    if (typeof data.request.errorMessage !== 'undefined') {
                        let message = data.request.errorMessage;
                        setScanErrorBar(message);
                    }
                }
                enableCardNumberField();
                clearInterval(transactionInterval);
            }
        }
    })
        .fail(function (failedResponse) {
            showErrorMessage(failedResponse.responseJSON.error_message);
        });
}

function getPaymentParameters() {
    return {
        api_token: strApiToken,
        service_id: strServiceId,
        transactionId: iTransactionId
    };
}

function getInputParameters() {
    let objParameters = {
        amount: iAmount,
        api_token: strApiToken,
        service_id: strServiceId,
        terminal_id: strTerminalId,
    };

    if (strDescription.length > 0) {
        objParameters.description = escape(strDescription.substring(0, 31));
    }

    if (strOrderNumber.length > 0) {
        objParameters.order_number = strOrderNumber;
    }

    return objParameters;
}

function setQrLoadingBarStatusPaid() {
    let text = jQuery('.startpin-qr-success-message').text();
    jQuery('#load-qr .loading-title').text(text)
        .removeClass('alert-info alert-warning')
        .addClass('alert-success');
    jQuery('#load-qr .progress-bar').removeClass('bg-danger bg-success bg-info bg-strong-warning')
        .addClass('bg-strong-success')
        .css({'width': '100%'});
}

function setQrLoadingBarStatusVerified() {
    let verifyMessage = jQuery('.startpin-transaction-status-verify').html();
    jQuery('#load-qr .loading-title').text(verifyMessage)
        .removeClass('alert-info')
        .addClass('alert-warning');
    jQuery('#load-qr .progress-bar').removeClass('bg-danger bg-success bg-info')
        .addClass('bg-strong-warning');
}

function setQrLoadingBarError(message) {
    blurQRCode();
    blurStatusIcons();
    jQuery('#load-qr .loading-title').text(message)
        .removeClass('alert-info alert-warning')
        .addClass('alert-danger');
    jQuery('#load-qr .progress-bar')
        .clearQueue().stop().animate({'width': '100%'}, middleAnimationDuration)
        .removeClass('bg-danger bg-success bg-info bg-strong-warning')
        .addClass('bg-strong-danger');
}

function startQRLoading() {
    animateLoading = jQuery('#load-qr .progress-bar').css({'width': '1%'}).animate({
        'width': '100%'
    }, 45000);
}

function finishQRPaymentProcessing() {
    clearInterval(transactionInterval);
    QR_VERIFY_BUTTONS.hide();
    enableStartAndDisableCancelButtons();
}

function setBalance() {
    let oldBalance = parseFloat(scanLoadingBalance.text().replace(',', '.'));
    let newBalance = currency(oldBalance).subtract(parseFloat(iAmount / 100));

    scanLoadingBalance.text(newBalance.toString().replace('.', ','));
}

function startGiftLoading(data) {
    scanLoadingBar.stop();
    scanLoadingBar.css({'width': '1%'}).animate({
        'width': '100%'
    }, 2000);

    setTimeout(function () {
        if (true == data.request.result) {
            setScanSuccessBar();
            setBalance();
            enableStartAndDisableCancelButtons();
            enableCardNumberField();
            enablePINCodeField();
        }
    }, 2000);
}

function activateScanLoading(data) {
    scanLoadingBar.stop();
    if (data.request.result == true) {
        let transactionId = data.transaction.transactionId;
        let state = BARCODE_STATUS_CODES[data.transaction.state];
        if (state === BARCODE_STATUS_CODE_PENDING) {
            statusCheck(transactionId);
            startScanLoading();
            disableStartAndEnableCancelButtons();
        } else if (state === BARCODE_STATUS_CODE_PAID) {
            enableStartAndDisableCancelButtons();
            enableCardNumberField();
            setScanSuccessBar();
        }
    }
}

function startScanLoading() {
    setScanDefaultBar();
    scanLoadingBlock.removeClass('hidden');
    scanLoadingBlock.show();
    scanLoadingBar.animate({
        'width': '100%'
    }, 45000);
}

function setScanSuccessBar() {
    finishScanBarLoading();
    let text = jQuery('.startpin-scan-success-message').text();
    scanLoadingTitle.text(text);
    scanLoadingTitle.removeClass('alert-info alert-warning')
        .addClass('alert-success');
    scanLoadingBar.removeClass('bg-danger bg-success bg-info bg-strong-warning bg-strong-danger')
        .addClass('bg-strong-success');
    enableStartAndDisableCancelButtons();
}

function setScanWarningBar() {
    let verifyMessage = jQuery('.startpin-transaction-status-verify').html();
    scanLoadingTitle.text(verifyMessage);
    scanLoadingTitle.removeClass('alert-info')
        .addClass('alert-warning');
    scanLoadingBar.removeClass('bg-danger')
        .removeClass('bg-danger bg-success bg-info bg-strong-danger')
        .addClass('bg-strong-warning');
}

function setScanCancelBar() {
    let message = jQuery('.startpin-scan-cancel-message').text();
    setScanErrorBar(message);
}

function setScanErrorBar(message) {
    finishScanBarLoading();
    scanLoadingTitle.text(message)
        .removeClass('alert-info alert-warning alert-success')
        .addClass('alert-danger');
    scanLoadingBar.removeClass('bg-danger bg-success bg-info bg-strong-warning bg-strong-success')
        .addClass('bg-strong-danger');
    enableStartAndDisableCancelButtons();
}

function setScanDefaultBar() {
    scanLoadingBar.clearQueue().stop().css({'width': '1%'});
    let text = jQuery('.startpin-scan-pending-message').text();
    scanLoadingTitle.text(text).removeClass('alert-danger').addClass('alert-info');
    scanLoadingBar.removeClass('bg-danger').removeClass('bg-success').removeClass('bg-info').removeClass('bg-strong-danger');
}

function finishScanBarLoading() {
    SCAN_VERIFY_BUTTONS.hide();
    scanLoadingBar.clearQueue()
        .stop()
        .animate({'width': '100%'}, middleAnimationDuration);
}

function statusCheck(transactionId) {
    scanLoadingBlock.show();
    let strUrl = '/ajax/checkScannedCardTransaction';
    let data = {
        transactionId: transactionId,
        apiToken: strApiToken,
        serviceId: strServiceId,
    };
    $.ajax({
        url: strUrl,
        data: data,
        headers: {'X-CSRF-TOKEN': csrfToken},
        type: "post",
        dataType: "json",
        cache: false,
        success: function (data) {
            if (typeof data.request !== 'undefined') {
                if (data.request.result === REQUEST_RESULT_SUCCESS) {
                    let stateName = data.paymentDetails.stateName;
                    if (stateName === BARCODE_STATUS_CODE_PAID) {
                        setScanSuccessBar();
                        enableCardNumberField();
                        clearInterval(transactionInterval);
                    } else if (stateName === BARCODE_STATUS_CODE_CANCEL) {
                        setScanCancelBar();
                        enableCardNumberField();
                        clearInterval(transactionInterval);
                    } else if (stateName === BARCODE_STATUS_CODE_EXPIRED) {
                        let message = jQuery('.startpin-transaction-status-expired').text();
                        setScanErrorBar(message);
                        clearInterval(transactionInterval);
                    } else if (stateName === BARCODE_STATUS_CODE_PENDING || stateName === BARCODE_STATUS_CODE_VERIFY) {
                        clearInterval(transactionInterval);
                        transactionInterval = setInterval(function () {
                            statusCheck(transactionId);
                        }, 3000);
                        if (stateName === QR_STATUS_CODE_VERIFY) {
                            setScanWarningBar();
                            SCAN_VERIFY_BUTTONS.show();
                        }
                    } else {
                        resetScanProgress();
                    }
                } else if (data.request.result === REQUEST_RESULT_FAILED) {
                    if (typeof data.request.errorMessage !== 'undefined') {
                        setScanErrorBar(data.request.errorMessage);
                    }
                }
            }
        }
    })
    .fail(function (failedResponse) {
        if (failedResponse.status === 403) {
            showErrorMessage(failedResponse.responseJSON.error_message);
        }
    });
}

/**
 * Set cookie values
 *
 * @returns {undefined}
 */
function saveFormData() {
    jQuery.ajax({
        url: '/ajax/storeFormData',
        type: "post",
        data: {
            'description': strDescription,
            'order_number': strOrderNumber,
            'api_token': strApiToken,
            'service_id': strServiceId,
            'default_terminal_id': strTerminalId,
            'toggle': boolToggle,
            'static_fields': boolStaticFields,
            'amount': iAmount
        },
        headers: {'X-CSRF-TOKEN': csrfToken},
        dataType: "json",
        cache: false,
    });

    return true;
}

function clearFormData() {
    jQuery.ajax({
        url: '/ajax/clearFormData',
        type: "post",
        headers: {'X-CSRF-TOKEN': csrfToken},
        cache: false,
    });
}

/**
 * Countdown timer
 *
 * @param {type} duration
 * @param {type} display
 * @returns {undefined}
 */
function startTimer(duration, span) {

    var timer = duration, minutes, seconds;
    transactionInterval = setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        transactionTimer = seconds;

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        span.text(minutes + ":" + seconds);

        if (--timer < 0) {
            cancelPayment.click();
            clearInterval(transactionInterval);
        }
    }, 1000);
}

function validateEmail(emailAddress) {
    let regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return regex.test(emailAddress);
}

function confirmPayment() {
    let strEmail = '';
    let iLanguage = 1;

    unsetConfirmError();

    if (doEmailTicket.is(':checked')) {
        strEmail = email.val();
        iLanguage = language.val();

        if (strEmail == '' || !validateEmail(strEmail)) {
            let strMessage = arrText['error_ticketInvalidEmail'];
            showConfirmError('error', strMessage, true);
            return false;
        }
    }

    jQuery.ajax({
      cache: false,
      dataType: 'jsonp',
      async: true,
      headers: {'X-CSRF-TOKEN': csrfToken},
      data: {
            'hash': strHash,
            'emailAddress': strEmail,
            'languageId': iLanguage
        },
      url: '/' + lang + '/ajax/confirmPayment',
        success: function (result) {
            let strMessage = '';
            let strMessageType = 'success';
            let bShowInputError = false;
            if (result.request.result == "1") {
                if (doEmailTicket.is(':checked')) {
                    strMessage = arrText['success_ticketSent'];
                } else {
                    strMessage = arrText['success_transactionConfirmed'];
                }
            } else {
                if (result.request.errorId == "PAY-9112") {
                    bShowInputError = true;
                }

                if (arrText['api_error_' + result.request.errorId]) {
                    strMessage = arrText['api_error_' + result.request.errorId];
                } else if (result.request.errorMessage) {
                    strMessage = result.request.errorMessage;
                } else {
                    strMessage = arrText['api_error_PAY-0'];
                }

                strMessageType = 'error';
            }
            showConfirmError(strMessageType, strMessage, bShowInputError);
        }
    });

    return false;
}

function showConfirmError(strMessageType, strMessage, bShowInputError) {
    if (strMessageType == 'success') {
        jQuery('.confirmBoxHide').hide();
        jQuery('#confirmBoxResult').html('<div class="progress-text text-center"><h4 class="status-approved">' + strMessage + '</h4></div>').show();
    } else {
        jQuery('#confirmBoxResult').html('<div class="progress-text text-center"><h4 class="status-error">' + strMessage + '</h4></div>').show();
        btnConfirmPayment.addClass('btn-danger');
        if (bShowInputError) {
            email.addClass('alert-danger');
        }
    }
}

function unsetConfirmError() {
    jQuery('#confirmBoxResult').html('');
    jQuery('.confirmBoxHide').show();
    email.removeClass('alert-danger');
    btnConfirmPayment.removeClass('btn-danger');
}

function resetConfirmationBlock() {
    email.val('');
    doEmailTicket.prop('checked', false);
    unsetConfirmError();
    disableConfirmationInputs();
}

function enableConfirmationInputs() {
    email.attr('disabled', false);
    btnConfirmPayment.attr('disabled', false);
    language.attr('disabled', false)
        .selectpicker('refresh');
}

function disableConfirmationInputs() {
    email.attr('disabled', true);
    btnConfirmPayment.attr('disabled', true);
    language.attr('disabled', true)
        .selectpicker('refresh');
}

function resetProgress() {
    setProgressBar(0);
}

function setProgress() {
    let percPerIteratie = iStartProgressPerSecond / (1000 / iProgressTimeout);
    let currProgress = progressBar.attr('aria-valuenow') * 1;
    currProgress += percPerIteratie;

    setProgressBar(currProgress);

    clearTimeout(rProgressTimeout);
    rProgressTimeout = setTimeout(function () {
        if (strTerminalStatus == PIN_STATUS_CODE_START || strTerminalStatus === PIN_STATUS_CODE_VERIFY) {
            setProgress();
        }
    }, iProgressTimeout);
}

function setProgressBar(progress) {
    progressBar.attr('aria-valuenow', progress).css({'width': progress + '%'});
}

function getStatus(strHash, recur) {
    jQuery.ajax({
        cache: false,
        dataType: getDataType(),
        async: true,
        url: strStatusApiUrl,
        success: function (result) {
            let arrStatusResponse = processStatusResponse(result);

            strTerminalStatus = arrStatusResponse.strTerminalStatus;
            strStatusPayment = arrStatusResponse.strStatusPayment;

            recur = setStatusStyle(strStatusPayment);
            if (result.ticket !== '') {
                prepareIFrame(result.ticket);
            }
            clearTimeout(rStatusTimeout);
            if (recur == true) {
                rStatusTimeout = setTimeout(function () {
                    getStatus(strHash, recur);
                }, iStatusTimeout);
            }
        }
    })
        .fail(function () {

            if (Date.now() < iFinish) {
                clearTimeout(rStatusTimeout);
                rStatusTimeout = setTimeout(function () {
                    getStatus(strHash, true);
                }, iStatusTimeout);
            }
        });
}

function processStatusResponse(result) {
    let strTerminalStatus = '';
    let strStatusPayment = '';
    if (strServiceVersion == 'PayInstore_v1') {
        strTerminalStatus = result.status;
        if (result.approved == 1) {
            strStatusPayment = PIN_STATUS_CODE_APPROVED;
            enableStartAndDisableCancelButtons();
        } else if (result.status == PIN_STATUS_CODE_DECLINED) {
            strStatusPayment = PIN_STATUS_CODE_DECLINED;
            enableStartAndDisableCancelButtons();
        } else if (result.cancelled == 1) {
            if (result.incidentcodetext === INCIDENT_CODE_TEXT_TIMEOUT_EXPIRATION) {
                strStatusPayment = PIN_STATUS_CODE_EXPIRED;
            } else {
                strStatusPayment = PIN_STATUS_CODE_CANCELLED;
            }
            enableStartAndDisableCancelButtons();
        } else if (result.error == 1) {
            strStatusPayment = PIN_STATUS_CODE_ERROR;
            enableStartAndDisableCancelButtons();
        } else if (result.status == PIN_STATUS_CODE_START) {
            strStatusPayment = PIN_STATUS_CODE_INIT;
        } else if (result.status === PIN_STATUS_CODE_VERIFY) {
            strStatusPayment = PIN_STATUS_CODE_VERIFY;
        }
    } else if (strServiceVersion == 'WIPay2') {
        strTerminalStatus = result.terminal.state;
        strStatusPayment = result.transaction.state;
    }

    return {
        strTerminalStatus: strTerminalStatus,
        strStatusPayment: strStatusPayment
    };
}

function setStatusStyle(status_payment) {
    if (arrStyleStatus[status_payment]) {
        jQuery('#statustext h4').attr('class', arrStyleStatus[status_payment]);
    }
    if (arrText['status-' + status_payment]) {
        jQuery('#statustext h4').text(arrText['status-' + status_payment]);
    }
    if (arrStyleProgress[status_payment]) {
        progressBar.attr('class', 'progress-bar ' + arrStyleProgress[status_payment]).show();
    }

    if (status_payment == PIN_STATUS_CODE_APPROVED) {
        confirmBox.show();
    } else if (status_payment === PIN_STATUS_CODE_VERIFY) {
        PIN_VERIFY_BUTTONS.show();
    } else if (status_payment === PIN_STATUS_CODE_EXPIRED) {
        PIN_VERIFY_BUTTONS.hide();
    }

    if (strTerminalStatus === PIN_STATUS_CODE_START || strTerminalStatus === PIN_STATUS_CODE_VERIFY) {
        return true;
    } else {
        cancelTransactionButton.hide();
        setProgressBar(100);
    }

    return false;
}

function resetScanProgress() {
    scanLoadingBlock.hide();
    SCAN_VERIFY_BUTTONS.hide();
    setScanDefaultBar();
    clearInterval(transactionInterval);
}

function cancelTransaction() {
    if (cancelPayment.attr('disabled') ||
        (strTerminalStatus !== PIN_STATUS_CODE_START && strTerminalStatus !== PIN_STATUS_CODE_VERIFY)) {
        return false;
    }

    jQuery.ajax({
        cache: false,
        dataType: getDataType(),
        async: true,
        url: strCancelApiUrl,
        success: function (result) {
            if (typeof result !== 'undefined' && result.error == '1') {
                showErrorMessage(result.errormsg);

                return;
            }
            resetVars();

            showStartNewTransaction();
            clearInterval(transactionInterval);
        }
    })
    .fail(function (result) {
        if (typeof result !== 'undefined' && result.error == '1') {
            showErrorMessage(result.errormsg);
        }
    });
}

function getDataType() {
    return strServiceVersion == 'PayInstore_v1' ? 'json' : 'jsonp';
}

function showErrorMessage(message) {
    errorBlock.find('p').html(message);
    actionBlock.hide();
    paymentProgressBlock.hide();
    errorBlock.show();
}

function showStartNewTransaction() {
    errorBlock.hide();
    paymentProgressBlock.hide();
    confirmBox.hide();
    resetConfirmationBlock();
    actionBlock.show();
}

function fillTransactionProgressBlock() {
    jQuery('#payment-amount span').html(jQuery("[name=amount]").myStripTags().val());
    jQuery('.loading-text').html(strDescription);
    jQuery('#startpin-description').html(strDescription);
    jQuery('#startpin-transaction-id').html(paymentTransactionId);
}

function parseQueryString(query) {
    let vars = query.split("&");
    let query_string = {};
    for (let i = 0; i < vars.length; i++) {
        let pair = vars[i].split("=");
        let key = decodeURIComponent(pair[0]);
        let value = decodeURIComponent(pair[1]);
        // If first entry with this name
        if (typeof query_string[key] === "undefined") {
            query_string[key] = decodeURIComponent(value);
            // If second entry with this name
        } else if (typeof query_string[key] === "string") {
            let arr = [query_string[key], decodeURIComponent(value)];
            query_string[key] = arr;
            // If third or later entry with this name
        } else {
            query_string[key].push(decodeURIComponent(value));
        }
    }

    return query_string;
}

function isKeyCodeDigitOrBackspace(event) {
    return (event.keyCode >= 48 && event.keyCode <= 57) || event.keyCode === 8;
}

function prepareIFrame(ticket) {
    jQuery.ajax({
        cache: false,
        method: 'post',
        data: { base64: ticket },
        headers: {'X-CSRF-TOKEN': csrfToken},
        async: true,
        url: '/pdf/generate',
        success: function (pdf) {
            let iframe = jQuery('#print-receipt');
            iframe.attr('src', 'data:application/pdf;base64,' + pdf);
            iframe.data('base64', pdf);
        }
    });
}
