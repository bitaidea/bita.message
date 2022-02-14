Laravel Message Package

Hi, we support [IpPanel](https://ippanel.com/) and [SmsIr](https://sms.ir/) providers that you can choose which one to use in bitamessage.php. <br>
**also** you can add new provider there!
 
How to install:
========
> composer require bita/message
>
> php artisan vendor:publish --provider="Bita\Message\MessageServiceProvider"

Setup:
------
add this line to your app.php providers :
> Bita\Message\MessageServiceProvider::class

and add this line to your app.php aliases:
> 'Message' =&gt; Bita\Message\Facades\Message::class

After publish the package files you must add your provider configuration in .env

### Like this:
> IP PANEL
>
>- BITA_MESSAGE_IP_PANEL_ENDPOINT=your provider end point
>
>- BITA_MESSAGE_IP_PANEL_ORIGINATOR=your line number
>
>- BITA_MESSAGE_IP_PAENL_API_KEY=your api key

> SMS IR
>
>- BITA_MESSAGE_SMS_IR_ENDPOINT=your provider end point
>
>- BITA_MESSAGE_SMS_IR_ORIGINATOR=your line number
>
>- BITA_MESSAGE_SMS_IR_API_KEY=your api key
>
>- BITA_MESSAGE_SMS_IR_SECRET_KEY=your secret key

How To Use:
-----
change default service in bitamessage.php and call provider configuration from .env in that.
if you want to log messages in database,set logs = true and set the table name in bitamessage.php.

### now to send message
> Message::send($message, array $numbers);

### to send message by pattern
> Message::sendByPattern($pattern, $number, array $parameters);

### to check delivery
> Message::checkDelivery($tracker_id);

### to get credit
> Message::credit();
