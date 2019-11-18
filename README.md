# CakePHP Resize images

Resize images in web application by url.

## Install Cookies
- Set resize routes from `config > routes.php` in yout **routes.php**
- Copy the `src > Controller > Resize` to your **Controller** folder.
- Copy the `src > View > Helper > ResizeHelper.php` to your **Helper** folder.

### Requirements
- PHP >= 7.1.x
- GD exension enabled
- CakePHP >= 3.6.x

### Composer
Add following statement in `require` area.
```json
{
    // ...
    "require": {
        "ext-gd": "*",
    }
    // ...
}
```

### Load in AppView
Load Resize helper in `src > View > AppView.php` in `initialize` method like below.
```php
public function initialize()
{
    // ...
    $this->loadHelper('Resize');
    // ...
}
```

## Image URL
Resize URL to use in template.

If you want multiple folder tree structure write path with '--' between folders.

### Example
```
Tree structure:
webroot
- files
--- media

Disk path: files/media
Resize folder path: files--media
```

Link parameter can be Null, True or False.
```php
public function url(string $imageName, string $folder, int $width, int $height, $link = null, string $type = 'normal')
```

## Image HTML element
Create `<img />` HTML element with resize URL as `src`.

If you want multiple folder tree structure write path with '--' between folders.

Options:
- `_full` - Full base url
- `proportional` - True or False
- any `<img />` tag option like: alt, border, style, class, etc.
```php
public function image(string $imageName, string $folder, int $width, int $height, array $options = [])
```

## Example
Resize `image.jpg` from `webroot > files > media` folder and get the resize url.
```php
echo $this->Resize->image('image.jpg', 'files--media', 150, 150, [
    'proportional' => true
]);

echo $this->Resize->url('image.jpg', 'files--media', 150, 150);
```

Enjoy ;)
