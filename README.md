Why Not Exceptions

http://www.lighterra.com/papers/exceptionsharmful/

https://stackoverflow.com/questions/1736146/why-is-exception-handling-bad
"The rule of thumb is that exceptions should flag exceptional conditions, and that you should not use them for control of program flow."

http://xahlee.info/comp/why_i_hate_exceptions.html

https://docs.microsoft.com/en-us/archive/blogs/larryosterman/structured-exception-handling-considered-harmful

https://www.joelonsoftware.com/2003/10/13/13/

https://dzone.com/articles/exceptions-as-controlflow-in-java

https://www.linkedin.com/pulse/exceptional-exceptions-demystified-part-i-ivan-pointer

I am of the mind that if a function is to return something, then it should return. Not return or throw. If an exception is exceptional, it should be catched and logged.