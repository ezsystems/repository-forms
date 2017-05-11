# Improved FieldType validation

FieldType validation is currently done using a `validate()` method implemented in the SPI FieldType.

It uses the FieldDefinition's ValidatorConfiguration, obtained using `getValidatorConfiguration()`,
that returns a custom validation configuration hash. Example:

```php
[
    'StringLengthValidator' => [
        'minStringLength' => 0,
        'maxStringLength' => 25
    ]
]
```

We can easily built any Constraint from the Validation component based on this configuration.
Our challenge is that validation is at _class_ level (using static annotations / methods).
In our case, validation depends almost entirely on the FieldDefinition.

Writing this validation using annotations would require an second pass of parsing to:
- enabling/disabling validators depending on the configuration
- configuring validator, still depending on the configuration.

## Ramblings

Do we have any way to do this using the validation component ? Or with minimal changes ?
We want to validate Content Structs. Create is reasonably easy, as we have the ContentType
at our disposal. Update doesn't have _any_ kind of link with the content it is meant for...
Should we use an aggregate object, like `ContentUpdate`, that would have the `ContentUpdateStruct`
_and_ the `VersionInfo` it is meant to update ? We need it to build the form anyway...

But how we we get our `Validator` to use the `FieldDefinition` to customize the validation process
for a FieldType ?

What about the `ObjectInitializerInterface` from the Validator component ? Doctrine uses it to 
pre-load proxy objects before validation. Seems like an object that is _told_ to do something
rather than asked for it. Likely dead end.

Lets look at how constraints are obtained. We could maybe do something with the `MetadataFactory`.
It is used by the Validator to get the Validated value's metadata.

And... what if a FieldType has multiple edit fields ? How are constraints given for one or the other ?
Those will always have their own FormType. The FormType will get the constraints when it gets built If they are indexed
by field, they can easily be forwarded to the child form items.
