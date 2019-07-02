<?php
/**
 * Resize Helper
 *
 * @link https://github.com/mrred85/cakephp-resize
 * @copyright 2016 - present Victor Rosu. All rights reserved.
 * @license Licensed under the MIT License.
 */

namespace App\View\Helper;

use Cake\View\Helper;

/**
 * @package App\View\Helper
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class ResizeHelper extends Helper
{
    public $helpers = ['Html', 'Url'];

    /**
     * Resize image url
     *
     * ### Type:
     *
     * - 'proportional'
     * - 'normal'
     *
     * @param string $imageName Image name
     * @param string $folder Folder path
     * @param int $width Image width
     * @param int $height Image height
     * @param null|string $link Full base URL
     * @param string $type Image crop type
     * @return array|string
     */
    public function url(string $imageName, string $folder, int $width, int $height, $link = null, string $type = 'normal')
    {
        if ($type == 'proportional') {
            $name = 'resize:imgProportional';
        } else {
            $name = 'resize:imgResize';
        }
        $imageInfo = pathinfo($imageName);
        $urlArr = [
            '_name' => $name,
            'folder' => $folder,
            'img' => $imageInfo['filename'],
            'img_ext' => $imageInfo['extension'],
            'dimension' => $width . 'x' . $height
        ];

        if ($link !== null) {
            return $this->Url->build($urlArr, ['fullBase' => $link === true]);
        }

        return $urlArr;
    }

    /**
     * Resize image HTML element
     *
     * ### Options:
     *
     * - 'proportional' = true|false
     * - '_full' = true|false
     *
     * @param string $imageName Image name
     * @param string $folder Folder path
     * @param int $width Image width
     * @param int $height Image height
     * @param array $options Image options
     * @return null|string
     */
    public function image(string $imageName, string $folder, int $width, int $height, array $options = [])
    {
        if (isset($options['proportional']) && $options['proportional'] === true) {
            $url = $this->url($imageName, $folder, $width, $height, null, 'proportional');
        } else {
            $url = $this->url($imageName, $folder, $width, $height, null);
        }
        $fullBase = (isset($options['_full']) && $options['_full'] === true ? (bool)$options['full'] : false);
        unset($options['proportional'], $options['fullBase'], $options['_full'], $options['full_base']);

        if ($url) {
            $options = array_merge([
                'width' => $width,
                'height' => $height,
                'fullBase' => $fullBase
            ], $options);

            return $this->Html->image($url, $options);
        }

        return null;
    }
}
