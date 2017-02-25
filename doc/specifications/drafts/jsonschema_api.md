# JSON Schema content forms

## Synopsis
An API endpoint that, for a content type identifier, responds with a content creation form
expressed using JSON schema, as specified in https://github.com/ezsystems/repository-forms/pull/113.

## Request/Response example

```bash
curl -X GET http://example.com/api/ezp/v2/content/create/article -H 'Accept: application/schema+json'
```

```json
{
    "title": "New Folder",
    "description": "Th most basic container",
    "type": "object",
    "required": [
        "name"
    ],
    "properties": {
        "name": {
            "type": "string",
            "title": "Name"
        },
        "short_name": {
            "type": "string",
            "title": "Short name"
        }
    }
}
```

### Piece by piece breakdown

- `title` and `description` are built from the Content Type identifier (article, folder...).
- `type` doesn't change
- `required` is built by iterating over the field definitions, and by adding those that are required
- `properties` is built from the fields. For each field definition:
  - The key (`name`, `short_name`) is the field definition identifier
  - `type` depends on the form widget type. What happens if there are several ?
  - `title` is the field definition's name
