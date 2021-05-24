<?php

return [
    'fields:show_all_fields:label' => 'Show all fields',
    'fields:amount:label' => 'Amount',
    'fields:description:label' => 'Description',
    'fields:api_token:label' => 'API Token',
    'fields:service_id:label' => 'Service ID',
    'fields:terminal_id:label' => 'Terminal ID',
    'startpin:transaction:label' => 'Description',
    'fields:fix_it:label' => 'Lock',
    'startpin:start_pins_action:title' => 'Welcome to MPOS.nl',
    'startpin:start_pins_action:text' => 'Enter your PAY. data and start a payment',
    'startpin:start_new_transaction:text' => 'A new transaction can be started',
    'buttons:start' => 'Start',
    'buttons:cancel' => 'Cancel',
    'startpin:validation_error:title' => 'An error occurred',
    'startpin:validation_error:text' => '',
    'fields:api_token:validation_error' => 'API token entered is invalid',
    'api_response:status_approved' => 'Payment approved',
    'api_response:status_declined' => 'Transaction denied',
    'api_response:status_cancelled' => 'Cancelled',
    'api_response:status_error' => 'An error occurred',
    'api_response:status_expired' => 'Payment expired',
    'api_response:status_init' => 'waiting for a customer',
    'api_response:status_verify' => 'Verify the transaction',
    'api_response:success_ticket_sent' => 'The PIN receipt has been sent to the provided email address.',
    'api_response:success_transaction_confirmed' => 'The transaction has been confirmed successfully.',
    'api_response:error_ticket_invalid_email' => 'The entered email address is incorrect.',
    'api_response:error_ticket_not_sent' =>
        'An error occurred while sending the PIN receipt. Please, try again',
    'api_response:api_error_PAY_0' => 'Unknown error occurred. Please, try again later',
    'api_response:api_error_PAY_9001' => 'Transaction not found',
    'api_response:api_error_PAY_9002' => 'The transaction has already been paid',
    'api_response:api_error_PAY_9003' => 'Transaction not found',
    'api_response:api_error_PAY_9004' => 'Transaction not found',
    'api_response:api_error_PAY_9101' => 'Transaction not found',
    'api_response:api_error_PAY_9107' => 'The transaction has not been completed yet',
    'api_response:api_error_PAY_9108' => 'Transaction not paid',
    'api_response:api_error_PAY_9111' => 'This transaction has already been confirmed',
    'api_response:api_error_PAY_9112' => 'The entered email address is incorrect',
    'startpin:confirm_box:confirm_send_ticket' => 'Would you like us to email the receipt for this transaction?',
    'startpin:confirm_box:email_placeholder' => 'Your email address',
    'startpin:confirm_box:button_send' => 'Send',
    'startpin:confirm_box:button_print_receipt' => 'Print the receipt',
    'startpin:scan-balance-title:message' => 'Balance',
    'startpin:scan-success:message' => 'The payment has been processed successfully',
    'startpin:scan-pending:message' => 'Waiting for the payment',
    'startpin:scan-error:message' => 'SCAN ERROR MESSAGE',
    'startpin:qr-error:message' => 'Sorry, something went wrong...',
    'startpin:qr-pending:message' => 'Please, scan the QR code',
    'startpin:qr-success:message' => 'QR-code payment successful',
    'fields:service_match:validation_error' => 'flied service match validatuin error',
    'fields:cardnumber:label' => 'Card number/Barcode',
    'fields:pincode:label' => 'Pin',
    'fields:cardnumber:validation_error' => 'Invalid card number',
    'fields:amount:validation_error' => 'The entered amount is invalid.',
    'fields:description:validation_error' => 'Invalid description',
    'fields:service_id:validation_error' => 'The selected service is invalid',
    'fields:terminal_id:validation_error' => 'An invalid terminal has been selected',
    'fields:ip_address:validation_error' => 'Invalid IP address',
    'error:could_not_start_transaction' => 'An error occurred while starting a payment',
    'error:could_not_approve_transaction' => 'The transaction cannot be approved',
    'error:could_not_decline_transaction' => 'The transaction cannot be declined',
    'error:could_not_find_service' => 'The requested service was not found',
    'error:could_not_find_terminal' => 'No associated terminal found',
    'error:terminal_in_use' =>
        '<p> Terminal %s is currently in use. A transaction will be aborted if no input from the end user is made for 45 seconds. </p> <p>Try again in <span id="timer" style = "font-size: 22px"></span></p>',
    'error:invalid_token' => 'The entered API token is invalid',
    'error:could_not_find_gift_card' => 'It was impossible to find a card with number %s',
    'error:not_enough_money' => 'The card balance is exceeded.',
    'message:for:description' => 'for',
    'startpin:scan-cancel:error' => 'SCAN cancelled',
    'startpin:qr-scanned-status:message' => 'Waiting for the customer&#039;s confirmation in the APP',
    'startpin:qr-confirmed-status:message' => 'Please, complete the payment in your banking APP',
    'startpin:product:message' => 'Developed by',
    'fields:order_number:label' => 'Order number',
    'fields:order_number:validation_error' => 'The entered order number is invalid'
];