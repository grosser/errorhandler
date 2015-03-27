The Errorhandler consists of 3 Layers.

#### User input ####
ErrorHandlerGW is an easy-to access, userfriendly interface to the ErrorHandler. It validates input and translates it to calls to the ErrorHandler.

#### Error Handling ####
ErrorHandler catches the Errors that come in from error\_reporting, decides if they should be handled or ignored, cleans the Error backtrace and puts all this information in the Error Object.

#### Error Display ####
An ErrorProcessor can register at the ErrorHandler and will be notified of any occuring Error. It uses the Error-Object to generate an output using an ErrorRender and sends/delivers/saves this Output.