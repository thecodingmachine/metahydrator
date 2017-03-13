# About hydrating

This package aims to help handling raw data (associative arrays of basic types) for editing and creating instances of
whatever-the-class-you-want.
Therefore, a class offering a solution to this purpose should implement interface `Hydrator`, ie methods `hydrateObject`
and `hydrateNewObject`.
- method `hydrateNewObject` should create an instance of requested class, using parsed data to set its values.
- method `hydrateObject` should edit a given object, setting its properties using parsed data.
This package offers a generic  implementation of `Hydrator`, `MetaHydrator`, designed to work specifically with
[Mouf](http://mouf-php.com/).

## How to use?

The main interest of this package is to avoid unpleasant and interminable hours of writing repetitive lines of code to
describe how to manage some innput data (ie validate, parse and instantiate from it), and instead make the creation of a
data handler a simple configuration task. Configuration, or should I use the terms "dependency injection". This is where
the power of Mouf comes particularly handy: this tool, featuring present library, will allow you to create your data
parsers/validators with only drag/drop/naming action, occasionally writing little pieces of code (if you wish to implement
your own atomic parsers or validators), but without duplication.

## How does it work?

### Data parsing

Class `MetaHydrator` uses instances of `HydratingHandlerInterface` to parse and validate raw input. Such an instance should
parse specific key(s) in input array (using a configured instance of `ParserInterface`), and may throw an exception
(`Invalid`).
In `HydratingHandler` implementation of `HydratingHandlerInterface`, one specific key in input array data is handled, ie
the value located at that key is parsed and, if parsing was successful, parsed value is validated. Keep in mind that this
validation should not consider other parsed values, since a handler does not wait for all values to be parsed before
checking for its handled value sanity.

### Data validation

An implementation of interface `ValidatorInterface` must implement method `validate`; this method will do nothing if input
data is correct, and shall throw a `InvalidValueException` if it is not. Such an exception needs an `innerErrorsMap` when
constructed. A well-formed errors map should be an associative array, keys being strings, and values each being either a
descriptor of the field error (such as a `DetailedErrorMessage`, for instance) or a well-formed errors map. More precisely,
its structure should be consistent with input data, where invalid fields would be replaced by those error descriptors.

### Applying parsed data

Using an inner simple hydrator (by default, TDBMHydrator seems to be a good choice), the parsed data can finally be used
in two different ways: You can whether apply it to an already existing object, (using implementation of method `hydrateObject`)
of your hydrator, or create a new instance of the class you wish to instanciate (method `hydrateNewObject`).
