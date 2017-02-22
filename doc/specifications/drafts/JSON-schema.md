# JSON schema specification

## Basic JSON schema:
```json
{
    "title": "A registration form",
    "description": "A simple form example.",
    "type": "object",
    "required": [
        "firstName",
        "lastName"
    ],
    "properties": {
        "firstName": {
            "type": "string",
            "title": "First name"
        },
        "lastName": {
            "type": "string",
            "title": "Last name"
        },
        "age": {
            "type": "integer",
            "title": "Age"
        },
        "password": {
            "type": "string",
            "title": "Password",
            "minLength": 3
        }
    }
}
```

## Fields definitions:
The list below specify the options for JSON schema, which might be useful from frontend perspective.

### TextLine:
also applies for textBlock - JSON schema doesn't provide information about if it's input or textarea. This can be added as custom option but it's not part of the JSON schema standard.

#### Properties:
- "type": "string" -> defines the field type
- "title": "the field title" -> the label for the input

#### Validation options:
- "minLength": {Integer} -> minimum text length
- "maxLength": {Integer} -> maximum text length
- "pattern": {RegExp} -> pattern to match text

#### Example:
```json
"firstName": {
    "type": "string",
    "title": "First name",
    "minLength": 5,
    "maxLength": 20,
    "pattern": "^[A-Z]"
},
```

### Integer

#### Properties:
- "type": "integer" -> defines the field type
- "title": "the field title" -> the label for the input

#### Validation options:
- "minimum": {Integer} -> minimum value
- "maximum": {Integer} -> maximum value
- "exclusiveMinimum": {Boolean} -> if true, then a numeric instance should not be equal to the value specified in "maximum"
- "exclusiveMinimum": {Boolean} -> if true, then a numeric instance shuuld not be equal to the value specified in "minimum"
- "multipleOf": {Integer} -> A numeric instance is only valid if division by this keyword's value results in an integer.

#### Example:
```json
"age": {
    "type": "integer",
    "title": "Age",
    "minimum": 18,
    "exclusiveMinimum": false,
    "maximum": 100,
    "exclusiveMaximum": true
},
```

### Float

#### Properties:
- "type": "number" -> defines the field type
- "title": "the field title" -> the label for the input

#### Validation options:
- "minimum": {Float} -> minimum value
- "maximum": {Float} -> maximum value
- "exclusiveMinimum": {Boolean} -> if true, then a numeric instance should not be equal to the value specified in "maximum"
- "exclusiveMinimum": {Boolean} -> if true, then a numeric instance shuuld not be equal to the value specified in "minimum"
- "multipleOf": {Float} -> A numeric instance is only valid if division by this keyword's value results in an integer.

#### Example:
```json
"float": {
    "type": "number",
    "title": "Float",
    "minimum": 18.5,
    "exclusiveMinimum": false,
    "maximum": 100.3,
    "exclusiveMaximum": true
},
```

### Checkbox:

#### Properties:
- "type": "boolean" -> defines the field type
- "title": "the field title" -> the label for the input

#### Example:
```json
"done": {
    "type": "boolean",
    "title": "Done?"
}
```

### DateAndTime:

#### Properties:
- "type": "string" -> defines the field type
- "format": "date-time" -> defines the field format
- "title": "the field title" -> the label for the input

#### Example:
```json
"datetime": {
    "type": "string",
    "format": "date-time"
},
```

### Date:

#### Properties:
- "type": "string" -> defines the field type
- "format": "date" -> defines the field format
- "title": "the field title" -> the label for the input

#### Example:
```json
"date": {
    "type": "string",
    "format": "date"
},
```

### EmailAddress:

#### Properties:
- "type": "string" -> defines the field type
- "format": "email" -> defines the field format
- "title": "the field title" -> the label for the input

#### Validation options:
- "pattern": {RegExp} -> pattern to match text

#### Example:
```json
"email": {
    "type": "string",
    "format": "email"
},
```

### Url:

#### Properties:
- "type": "string" -> defines the field type
- "format": "uri" -> defines the field format
- "title": "the field title" -> the label for the input

#### Validation options:
- "pattern": {RegExp} -> pattern to match text

#### Example:
```json
"url": {
    "type": "string",
    "format": "uri"
},
```

### Selection/Country (single selection):

#### Properties:
- "type": "string" -> defines the field type
- "title": "Single selection" -> the label for the input
- "enum": ["option1","option2","option3"] -> list of the defined options

#### Example
```json
"singleSelection": {
    "type": "string",
    "title": "Single Selection",
    "enum": ["option1","option2","option3"]
},
```

### Selection/Country (multiple choices):

#### Properties:
- "type": "array" -> defines the field type
- "title": "Multiple Choices" -> the label for the input
- "items": {
      "type": "string", -> the type of the options
      "enum": ["option1","option2","option3"] -> list of the defined options
  }
- "uniqueItems": {Boolean} -> if true, then the selection must contain unique elements

#### Example
```json
"multipleChoicesList": {
    "type": "array",
    "title": "A multiple choices list",
    "items": {
        "type": "string",
        "enum": ["option1","option2","option3"]
    },
    "uniqueItems": true
},
```

### MapLocation:

#### Properties:
- "type": "object" -> defines the field type
- "title": "A Map Location" -> the label for the input
- "required": ["latitude", "longitude", "address"] -> the required properties for the field
- "properties": {
      "latitude": {
          "type": "number"
      },
      "longtitude": {
          "type": "number"
      },
      "address": {
          "type": "string"
      }
  }

#### Example
```json
"mapLocation": {
    "title": "A localisation form",
    "type": "object",
    "required": [
        "latitude",
        "longitude",
        "address"
    ],
    "properties": {
        "latitude": {
            "type": "number"
        },
        "longtitude": {
            "type": "number"
        },
        "address": {
            "type": "string"
        }
    }
}
```

### BinaryFile/Media (Single file):
The JSON schema standard doesn't provide information about maxFileSize, this has to be added by custom option.

#### Properties:
- "type": "string" -> defines the field type
- "format": "data-url" -> the field format
- "title": "Single file" -> the label for the input

#### Example
```json
"file": {
    "type": "string",
    "format": "data-url",
    "title": "Single file"
}
```

### BinaryFile/Media (Multiple files):
The JSON schema standard doesn't provide information about maxFileSize, this has to be added by custom option.

#### Properties:
- "type": "array" -> defines the field type
- "title": "Multiple files" -> the label for the input
- "items": {
      "type": "string",
      "format": "data-url"
  }

#### Example
```json
"files": {
    "type": "array",
    "title": "Multiple files",
    "items": {
        "type": "string",
        "format": "data-url"
    }
}
```
