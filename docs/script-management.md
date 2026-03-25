# Script Management

## Available Script Attributes

According to orestbida.com there are the following script attributes available:

`data-category`: name of the category

`data-service` (optional): if specified, a toggle will be generated in the preferencesModal

`data-type` (optional): custom type (e.g. "module")

`data-src` (optional): can be used instead of src to avoid validation issues

Example usage:
```html
<script
    type="text/plain"
    data-category="analytics"
    data-service="Google Analytics"
>/*...code*/</script>
```

For further information have a look at the [Cookie Consent Documentation - Script Management](https://cookieconsent.orestbida.com/advanced/script-management.html)