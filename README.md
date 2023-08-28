# Error Handler

Error handling with PSR-3 logging for catching exceptions as well as errors. There is also an error class to use as an 
alternative to throwing an exception.

## Example
```php
use Qubus\Error\Error;

use function Qubus\Error\Helpers\is_error;

function get_current_user(string|int $id = null)
{
    if(null === $id) {
        return new Error('User id cannot be null.');
    }
}

$user = get_current_user('56');

if(is_error($user)) {
    // log error message or send error flash message back to the user.
}
```

## Requirements
* PHP 8.2+

## Installation
```bash
$ composer require qubus/error
```

## Exceptions vs. Errors
There is a healthy discussion among developers whether to use exceptions or error return codes. There are pros and cons 
to both sides. No matter what side you fall on, the articles below are a great read with a dose of great examples as to 
the proper use of exceptions.

- [Exception Handling Considered Harmful](http://www.lighterra.com/papers/exceptionsharmful/)
- [Why is exception handling bad?](https://stackoverflow.com/questions/1736146/why-is-exception-handling-bad)
  - "The rule of thumb is that exceptions should flag exceptional conditions, and that you should not use them for control of program flow."
- [Why I Hate Exceptions](http://xahlee.info/comp/why_i_hate_exceptions.html)
- [Structured Exception Handling Considered Harmful](https://docs.microsoft.com/en-us/archive/blogs/larryosterman/structured-exception-handling-considered-harmful)
- [Exceptions](https://www.joelonsoftware.com/2003/10/13/13/)
- [Why You Should Avoid Using Exceptions as the Control Flow in Java](https://dzone.com/articles/exceptions-as-controlflow-in-java)
- [Exceptional Exceptions, Demystified - Part 1](https://www.linkedin.com/pulse/exceptional-exceptions-demystified-part-i-ivan-pointer)
- [Exceptional Exceptions, Demystified - Part 2](https://www.linkedin.com/pulse/exceptional-exceptions-demystified-part-2-ivan-pointer)
- [Exceptional Exceptions Demystified - Part 3](https://www.linkedin.com/pulse/exceptional-exceptions-demystified-part-3-ivan-pointer?_l=en_US)

## Author Statement
I am of the mind that if a function is to return something, then it should return, not throw. If an exception is 
exceptional, it should be caught and logged. I know that I don't do this perfectly, but it is my goal to go through 
all of my code and make sure that exceptions are exceptional and not unexceptional.