#MPOS.nl User Documentation#

Using MPOS you can initiate transactions via ***QR-code, Scan/Bar, and PIN terminal*** via ATMs that are provided or installed by PAY without having to use a cash register system. You open the responsive screen on a computer at your counter or on your smart-phone. You need the Internet connection if you want the device to work.

**Pin Transactions**

The initial screen looks like this. In the upper left corner, there is a *language switch drop-down*. The available languages are `NL` and `EN`. To the right from the language switcher, there's a toggle *Show all fields*. When this toggle is active - all the fields are shown (see the screen below). The fields `Amount`, `Description`, `API token` are required for data entering. The field `Order number` is optional for filling-in. The `Terminal ID` field is updated once an `API token` is entered. This field is only available for *PIN transactions*.


![start_screen_NL](images/start_screen_NL.jpg)

---

* `Amount` - the amount of a transaction.
* `Description` is shown on the consumer's statement and in your PAY. transaction overview.
 To correctly reconcile PIN transactions in your bookkeeping, you can give each transaction an `order number`. This is the order number of, for example, the invoice or the order in your cash register system.
* `API key/token` contains from 32 to 64 characters and enables you to start a PIN transaction. 
* `Service ID` belongs to your Sales location. A service-ID of a transaction is linked to the bbh Sales location.
* `Terminal ID` can be selected in the PAY. terminal overview with the help of the drop-down menu.


 ![start_screen_EN](images/start_screen_EN.jpg)

---

If the toggle *Show all fields* is off, only the following fields will be visible (see screen-shot below):

![show_all_fields_button_disabled](images/show_all_fields_disabled.jpg)

---

The toggle *Lock* is used for keeping the entered data. With this functionality you can store all the settings below. This way you do not have to enter everything all over again for the next PIN transaction. If you want to start a transaction on a different terminal, select a different Terminal-ID from the drop-down options. For the ease of use, there's search functionality for `Service ID` and `Terminal ID`. 

![lock_functionality](images/lock_functionality.jpg)

---

Once a user fills-in the `API token`, the system uploads the `Service ID` and `Terminal ID`. 

![correct_api_token](images/correct_API_token_behavior.jpg)

---

If an invalid `API token` is entered, the `Service ID` and `Terminal ID` are not uploaded. The button *Start* is clickable but the following error appears. 

![error_invalid_api_token](images/invalid_API_token_error.jpg)

---

PIN transactions should be blocked for a user if there's no `Terminal ID` chosen/available. Once a user switches to QR-code or Scan/Bar tab, it will be impossible to go back to PIN (unless the valid `API token` is entered and `Terminal ID` is connected).

![pin_tab_blocked](images/pin_tab_blocked_no_terminal.jpg)

---

When all the fields are filled-in with the correct data, the transaction can be initiated. To initiate it, click the button *Start*. Once pushed, the button becomes unclickable while the button *Cancel* becomes active and can be used to stop the transaction.

![transaction_status_init](images/transaction_status_init.jpg)

---

Once a transaction has started, a user has 45 seconds to make an action (approve or decline it). When the time is up and no action was made, the following message appears on the screen.

![transaction_status_expired](images/transaction_status_expired.jpg)

---

When an initiated transaction is approved by a client via the terminal, the following screen should appear. It is possible to send a receipt to the customer by e-mail. You can do this by adding the customer's email address and then clicking the Send button. The language can be configured for the corresponding e-mail to the nationality of a customer.

 ![transaction_status_approved](images/transaction_status_approved.jpg)

---

It is possible to print the receipt. Click on the blue *Print receipt* button. When you have clicked on the button, the dialog box of your computer opens. No special receipt printer is required to print these receipts.

 ![print_the_receipt](images/print_the_receipt.jpg)

---

If you want to save / download the pin receipt on your computer after printing, you can click on the arrow pointing down at the top right corner of the grey po-pup.

 ![download_the receipt](images/download_the_receipt.jpg)
 
 ---
 
 If a user declines a transaction via the terminal, the following message will appear.
 
![transaction_status_canceled](images/transaction_status_cancelled.jpg)

---

If a user is trying to initiate several transactions via the same terminal simultaneously (e.g., in different tabs), the following message will appear.

![terminal_X_is_in_use](images/terminal_X_is_in_use.jpg)

---

**###Scan/Bar payments###**

Scan/bar is used to process WeChat, Alipay, and GiftCards payments. These are the starting pages for the Scan/Bar payments processing. In the upper left corner, there is a *language switch drop-down*. The available languages are `NL` and `EN`. To the right from the language switcher, there's a toggle *Show all fields*. When this toggle is active - all the fields are shown (see the screen below). The fields `Amount`, `Description`, `API token` are required for data entering. The field `Order number` is optional for filling-in. 
Scan/bar payments can be done via the following menu of MPOS:

![scanbar_start_screen_NL](images/scanbar_start_screen_NL.png)


![scanbar_start_screen_EN](images/scanbar_start_screen_EN.png)

* `Amount` - the amount of a transaction.
* `Description` is shown on the consumer's statement and in your PAY. transaction overview.
 To correctly reconcile PIN transactions in your bookkeeping, you can give each transaction an `order number`. This is the order number of, for example, the invoice or the order in your cash register system.
* `API key/token` contains from 32 to 64 characters and enables you to start a PIN transaction. 
* `Service ID` belongs to your Sales location. A service-ID of a transaction is linked to the bbh Sales location.
* `Card-number/Barcode` - the field for entering the card number.

---

If the toggle *Show all fields* is off, only the following fields will be visible (see screen-shot below):

 ![scanbar_show_all_fields_disabled](images/scanbar_show_all_fields_disabled.png)
 
---

The toggle *Lock* is used for keeping the entered data. With this functionality you can store all the settings below. This way you do not have to enter everything all over again for the next transaction.  For the ease of use, there's search functionality for `Service ID`.

 ![scanbar_lock_is_enabled](images/scanbar_lock_is_enabled.png)

---

**WeChat Payment Example**

WeChat scan-data has 18 digits and starts with `10`, `11`, `12`, `13`, `14` or `15`. If the entered number is less than 18 (or more than 18; or starts with another number than required), the following error appears.

![scanbar_invalid_card_number_we_chat](images/scanbar_invalid_card_number_we_chat.png)

---

If the card-number is correct, the screen will look like this.

![scanbar_valid_card_number_wechat](images/scanbar_valid_card_number_wechat.png)

---

Once all the fields are filled with the correct data, it is possible to start a transaction. After clicking the button *Start* the user will see the following screen:

![scanbar_transaction_status_init](images/scanbar_transaction_status_init.png)

---

If a transaction was `Canceled`, the user will be informed about the change of the status.

![scanbar_transaction_status_canceled](images/scanbar_transaction_status_canceled.png)

---

Once a payment is successfully completed, the status of the transaction will be `Paid`.

![scanbar_transaction_status_successful](images/scanbar_transaction_status_successful.png)


If a transaction was started and the final result is not known, the status will be `Pending`.  

---

**AliPay Payment Example**

AliPay  scan-data has between 16 and 24 digits and starts with `26`, `27`, `28`, `29` or `30`. If one of these requirements is broken, the user will be informed of an error.

![scanbar_invalid_card_number_alipay](images/scanbar_invalid_card_number_alipay.png)

---

For the correct data, the following screen will appear:

![scanbar_valid_card_number_alipay](images/scanbar_valid_card_number_alipay.png)

The statuses for AliPay payments will be the same as for WeChat: `Paid`, `Canceled`, `Pending`.

---

**GiftCard Payment Example**

Via MPOS, it is also possible to make a gift-card payment or just check the balance of the voucher. To check the balance, it is enough to enter the `19-digit` card number of the gift card.

![scanbar_giftcard_balance](images/scanbar_giftcard_balance.png)

---

If a user wants to make a payment with a gift-card, it is necessary to fill-in all the fields (but for order number which is optional). If the password was not entered or was entered incorrectly, the following error will appear:

![scanbar_pincode_error](images/scanbar_pincode_error.png)

---

Once the correct credentials are entered, the payment can be initiated. After clicking the button *Start*, the payment cannot be canceled. Once the transaction started, a user will see the screen:

![scanbar_transaction_status_init_giftcard](images/scanbar_transaction_status_init_giftcard.png)

A user will be informed about a `Successful` transaction. Also, the refreshed account balance will be displayed.

![scanbar_transaction_status_successful_giftcard](images/scanbar_transaction_status_successful_giftcard.png)


**####QR-code Payments####**


MPOS makes QR payments possible. These are the starting pages for the QR payments processing. In the upper left corner, there is a *language switch drop-down*. The available languages are `NL` and `EN`. To the right from the language switcher, there's a toggle *Show all fields*. When this toggle is active - all the fields are shown (see the screen below). The fields `Amount`, `Description`, `API token` are required for data entering. The field `Order number` is optional for filling-in. 

![qr_start_screen_NL](images/qr_start-screen_qr_NL.png)

![qr_start_screen_EN](images/qr_start_screen_qr_EN.png)

* `Amount` - the amount of a transaction.
* `Description` is shown on the consumer's statement and in your PAY. transaction overview.
 To correctly reconcile PIN transactions in your bookkeeping, you can give each transaction an `order number`. This is the order number of, for example, the invoice or the order in your cash register system.
* `API key/token` contains from 32 to 64 characters and enables you to start a PIN transaction. 
* `Service ID` belongs to your Sales location. A service-ID of a transaction is linked to the bbh Sales location.

---
If the toggle *Show all fields* is off, only the following fields will be visible (see screen-shot below):

 ![qr_show_all_fields_disabled](images/qr_show_all_fields_disabled.png)
 
 ---
 
The toggle *Lock* is used for keeping the entered data. With this functionality you can store all the settings below. This way you do not have to enter everything all over again for the next transaction.  For the ease of use, there's search functionality for `Service ID`.
 
 ![qr_lock_functionality](images/qr_lock_functionality.png)

---

When the correct credentials are entered, the payment process can begin. After clicking the button *Start*, the payment is `Initiated`.

![qr_transaction_status_init](images/qr_transaction_status_init.png)

---

Once the QR code appears on the screen, it is supposed to be scanned with the device. After this, the status changes to `Scanned`. A user can see the Payment Method which will be used for the payment. In the example presented, iDEAL is used.

![qr_transaction_status_scanned](images/qr_transaction_status_scanned.png)

---

When a customer confirms the payment via the app on mobile,the status changes to `Confirmed`. The payment has to be completed via the banking app. 

![qr_transaction_status_confirmed](images/qr_transaction_status_comfirmed.png)

---

In case of success, the status of the payment changes to `Paid`. The user sees the screen below.

![qr_transaction_status_paid](images/qr_transaction_status_paid.png)

---

A user can decide to **cancel** the payment after the status `Confirmed`. Doing so, the payment will receive the status `Canceled` and the following screen will appear. Sometimes, due to the security requirements, a customer may be asked to make additional verification after app confimation. The status will be `Verify`. In such cases, the user will see this:

![qr_transaction_status_verify](images/qr_transaction_status_verify.png)

---

If the customer decides to `Approve`, the successful payment will be processed. The status of the transaction becomes `Paid`.

![qr_transaction_status_approved](images/qr_transaction_status_approved.png)

---

If the customer decides to `Decline`, the the payment will not be processed. The status of the transaction becomes `Denied`.

![qr_transaction_status_denied](images/qr_transaction_status_denied.png)

---