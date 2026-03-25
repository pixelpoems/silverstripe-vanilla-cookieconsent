# Custom Styling

If you want to overwrite the default styling which is loaded from the js library, you can do this by adding the following CSS to your project:

```scss
// Prefs Window
#cc-main {
    --cc-btn-border-radius: 0px;
    --cc-btn-primary-bg: var(--color-primary);
    --cc-btn-secondary-bg: var(--color-secondary);
    font-family: var(--font-base);

    .pm--box{
        border-radius: 0;
    }

    .pm__header{
        .pm__title{
            font-size: var(--fs-md);
        }
    }

    .pm__body{
        .pm__section-title{
            font-size: var(--fs-sm);
        }
    }

    .pm__btn {
        border: 0px;
        padding: var(--btn-padding-y) var(--btn-padding-x);
        font-size: var(--btn-font-size);
    }

    .pm__footer{}
}
```

Or have a look here: https://cookieconsent.orestbida.com/advanced/ui-customization.html