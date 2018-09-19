Cyclos 4 examples for PHP web services clients
----------------------------------------------

In this package, the API library for integrating 3rd party software with the
Cyclos 4 software (http://www.cyclos.org), as well as some examples, are 
provided for clients written in the PHP programming language. 
For examples of integration with other languages, please refer to
http://www.cyclos.org/documentation/webservices/


Dependencies
------------

* PHP 5.3 or newer (namespaces are used)
* PHP CURL extension (package php5-curl in Debian / Ubuntu)
* PHP JSON extension (package php5-json in Debian / Ubuntu)

Integrating Cyclos 4 services with your software
------------------------------------------------
The PHP client for Cyclos 4 uses a WEB-RPC mechanism, which is basically
HTTP POSTs, passing and receiving JSON objects. This is similar to REST web
services, but, distinct to that, each database record doesn't have an unique
URL. Instead, the service methods are executed by informing the operation name
and parameters to an URL. Each service is published under a distinct URL.

The provided PHP client, however, deals with the inner complexity of performing
the requests - a PHP class is generated for each Cyclos service interface, and
all methods are generated on them. The parameters and result types, however,
are not generated, and are either handled as strings, numbers, booleans or
generic objects (stdclass).

In order to use the Cyclos classes, we first register an autoload function to
load the required classes automatically, like this:

function load($c) {
    if (strpos($c, "Cyclos\\") >= 0) {
        include str_replace("\\", "/", $c) . ".php";
    }    
}
spl_autoload_register('load');

Then, Cyclos is configured with the server root URL and authentication details: 

Cyclos\Configuration::setRootUrl("http://192.168.1.27:8888/england");
Cyclos\Configuration::setAuthentication("admin", "1234");

The examples provided include the configureCyclos.php file, which does that, so
each example doesn't need to set the autoload function and the Cyclos settings.

Afterwards, services can be instantiated using the new operator, and the 
corresponding methods will be available:

$userService = new Cyclos\UserService();
$page = $userService->search(new stdclass());

Some examples are provided in their respective PHP files in this package.


Available services
------------------

To get the full list of available services, refer to JavaDocs provided in
the download bundle, on <cyclos-x.x.x.x>/web-services/javadoc/index.html.

Focus specially in the org.cyclos.services.* packages, as they describe all
service interfaces, the methods and parameters for each operation.

Note: Whenever a service requires a SerializableInputStream (actually means a 
file content), the PHP client (actually, the underlying WEB-RPC mechanism) 
sends / receives BASE64-encoded strings. This has advantages, like being very
portable, but also has drawbacks, like requiring the entire file content to be
loaded in memory.


Error handling
--------------

All errors thrown by the server are translated into PHP by throwing 
Cyclos\ServiceException. This class has the following properties:

* service: The service path which generated the error. For example, 
  paymentService, accountService and so on.

* operation: The name of the operation which generated the error. Is the same
  name as the method invoked on the service.

* errorCode: Is the simple Java exception class name, uppercased, with the word
  'Exception' removed. Check the API (as described above) to see which 
  exceptions can be thrown by each service method. Keep in mind that many times
  the declared exception is a superclass, of many possible concrete exceptions.
  All methods declare to throw FrameworkException, but it is abstract, and is
  implemented by several concrete exception types, like PermissionException. In
  this example, the errorCode will be PERMISSION. Another example is the
  InsufficientBalanceException class, which has as errorCode the string
  INSUFFICIENT_BALANCE.

* error: Contains details about the error. Only some specific exceptions have
  this field. For example, if the errorCode is VALIDATION, and the exception
  variable name $e, $e->error->validation will provide information on errors
  by property, custom field or general errors.


Server side configuration to enable web services
------------------------------------------------

For clients to invoke web services in Cyclos, the following configuration needs
to be done on the server (as global or network administrator):

* On the System management > Configurations tab, click a row to go to the
  configuration details page.
  
* On the Channels tab, click on the Web services channel row, to go to the 
  channel configuration details page.
  
* Make sure the channel is enabled. Click the edit icon on the right if the 
  channel is not defined on this configuration. Then mark the channel as 
  enabled, choose the way users will be able to access this channel (by default
  or manually) and the password type used to access the web services channel.
  You can also set a confirmation password, so sensitive operations, like
  performing a payment, will require that additional password.

* For the user which will be used for web services, on the view user profile
  page, under the User management box, click the channels access link.

* On that page, make sure the Web services channel is enabled for that user.
  Also, only active users may access any channel - on the profile page, on the
  same User management box, there should be a link with actions like Enable /
  Block / Disable / Remove. On that page, make sure the user status is Active.
  
* A side note: If performing payments via Web services, make sure the desired
  Transfer type is enabled for the Web services channel. To check that, go
  to System management > Accounts configuration > Account types. Then click
  the row of the desired account type, select the Transfer types tab and click
  on the desired payment type (generated types cannot be used for direct 
  payment). There, make sure the Channels field has the Web services channel.
