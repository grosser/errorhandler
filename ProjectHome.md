# PHP ErrorHandler #

![http://pragmatiker.net/alotta/user/pragmatiker.net/img/000/009//9686.jpg](http://pragmatiker.net/alotta/user/pragmatiker.net/img/000/009//9686.jpg)

## Overview ##
  * Complete, Readable, Secure Error Backtrace
  * **Ignoring of unwanted Errors**(you can develop with E\_STRICT, and dont need to @ all library calls)
  * can **halt the execution after the first Error**, preventing database corruption/data-loss
  * read current errors in your RSS Reader
  * have a full backtrace in your Logfiles
  * output your Errors to **HTML + Log + RSS + Mail** simulanously
  * works on **console+webpage** without extra configuration

See "Featured Wiki Pages" for more information

## Status ##
Public beta, please download and try!
(next version will have a version number..)

```
require_once('/home/xxx/ErrorHandlerGW.class.php');
ErrorHandlerGW::initialize(E_ALL^E_STRICT);
//ErrorHandlerGW::report('bail');//halt after first error
//ErrorHandlerGW::ignore(array('pear'=>E_STRICT,'simpletest'=>E_STRICT^E_NOTICE))
```

## Contribute ##
Any contributers are welcome! Just tell me what you want to add and ill exlain the architecture/test-setup if you like.

Needed Features
  * output as a JS popup window
  * refactor Mailer code to not need pear ?
