Herbert Rewrite
===============

Welcome to the Herbert rewrite branch. This version of Herbert is aimed at solving this [issue](https://github.com/getherbert/herbert/issues/7).

The first version of the framework could not run in conjunction with another plugin also using Herbert. We've solved this by only allowing one instance of the framework to load. Therefore one instance handles all plugins needing it.

If only once instance of Herbert is allowed to run then it proposes a problem for example:
```
Plugin 1 uses Herbert vX
Plugin 2 uses Herbert vY
```

In this case if Herbert vX doesn't have a method or support some feature that Plugin 2 requires then it would cause errors. This rewrite doesn't include our solution for this. We suggest, if everyone agrees, that we namespace versions. Like so:
```
Herbert\Framework\vX
```

Therefore in the above situation one instance of Herbert vX & vY would run.

## Folder Structure

Instead of forcing a file structure we've left it open. Some users may prefer to do PSR4 autoload by composer for there Controllers & Model whereas others use classmap.

Controllers & Models require a namespace for your plugin:

```
Vendor\Plugin
```

Standard files similar to first version like `routes.php` can be included using the config file:

```
'routes' => [
    __DIR__ . '/routes.php'
]
```

To avoid confusing new users of the framework we propose having two repos:

### getherbert/herbert
This repo will have the file structure set up similar to that of the original version of Herbert. Making it an easy starting place for most users.

### getherbert/herbert-slim
This repo will only contain the necessary files to get started so it suits someone wanting to set up there own file structure.

## Other Improvements

* The framework is now separate and pulled in by composer. Located [here](https://github.com/getherbert/framework)
* Removed Traits and now we use the `Container` from `Illuminate/Support`
* Being able to override Twig Provider

## Feedback

We want to know if this is the right direction for the framework so please let us know what you think. Post any [feedback here](https://github.com/getherbert/herbert/issues/14)
