<?php
/**
 * Resize Component
 *
 * @link https://github.com/mrred85/cakephp-resize
 * @copyright 2016 - present Victor Rosu. All rights reserved.
 * @license Licensed under the MIT License.
 */

namespace App\Controller\Resize;

use Cake\Controller\Controller;

/**
 * @package App\Controller\Resize
 */
class ResizeController extends Controller
{
    /**
     * Resize the image by url
     *
     * @param string $url Image path
     * @param string $dimension Image dimensions (WxH)
     * @param bool $proportional Crop proportional
     * @return void
     */
    private function _resize(string $url, string $dimension, bool $proportional = false)
    {
        if ($url && $dimension) {
            $imgDimension = explode('x', trim($dimension));
            $width = (int)$imgDimension[0];
            $height = (int)$imgDimension[1];
            if ($width && $height) {
                $size = getimagesize($url);
                $originalWidth = $size[0];
                $originalHeight = $size[1];
                $mime = $size['mime'];
                $ratio = $originalWidth / $originalHeight;

                $originalImage = null;
                switch ($mime) {
                    case 'image/jpeg':
                        $originalImage = imagecreatefromjpeg($url);
                        break;
                    case 'image/png':
                        $originalImage = imagecreatefrompng($url);
                        break;
                    case 'image/gif':
                        $originalImage = imagecreatefromgif($url);
                        break;
                }

                if (!$width && !$height) {
                    $width = $originalWidth;
                    $height = $originalHeight;
                }
                if (!$width) {
                    $width = round($height * $ratio);
                }
                if (!$height) {
                    $height = round($width / $ratio);
                }

                $x = $y = 0;
                $cropImage = imagecreatetruecolor($width, $height);
                if ($width && $height) {
                    if ($proportional) {
                        if ($originalWidth > $originalHeight) {
                            $x = -((($originalWidth / ($originalHeight / $height)) / 2) - ($width / 2));
                            $width = $originalWidth / ($originalHeight / $height);
                        } elseif ($originalWidth <= $originalHeight) {
                            $y = -((($originalHeight / ($originalWidth / $width)) / 2) - ($height / 2));
                            $height = $originalHeight / ($originalWidth / $width);
                        }
                        if ($mime != 'image/png') {
                            imagefill($cropImage, 0, 0, imagecolorallocate($cropImage, 255, 255, 255));
                        }
                    } else {
                        $widthRatio = $width / $originalWidth;
                        $heightRatio = $height / $originalHeight;
                        if ($widthRatio > $heightRatio) {
                            $resizeWidth = $width;
                            $resizeHeight = round($originalHeight * $widthRatio);
                        } else {
                            $resizeWidth = round($originalWidth * $heightRatio);
                            $resizeHeight = $height;
                        }
                        $x = round(($width - $resizeWidth) / 2);
                        $y = round(($height - $resizeHeight) / 2);
                        $width = $resizeWidth;
                        $height = $resizeHeight;
                        unset($resizeWidth, $resizeHeight);
                    }
                }
                imagealphablending($cropImage, false);
                imagecopyresampled($cropImage, $originalImage, $x, $y, 0, 0, $width, $height, $originalWidth, $originalHeight);
                imagesavealpha($cropImage, true);

                ob_start();
                switch ($mime) {
                    case 'image/jpeg':
                        imagejpeg($cropImage, null, 85);
                        break;
                    case 'image/png':
                        imagepng($cropImage, null, 8);
                        break;
                    case 'image/gif':
                        imagegif($cropImage, null);
                        break;
                }
                imagedestroy($originalImage);
                imagedestroy($cropImage);
                $imageBody = ob_get_contents();
                ob_end_clean();

                $response = $this->getResponse();
                if (!strstr($url, '://')) {
                    $response = $response->withModified(filemtime($url));
                }
                $response = $response
                    ->withType($mime)
                    ->withExpires('+30 days')
                    ->withMaxAge(604800)
                    ->withMustRevalidate(true)
                    ->withEtag(md5($url), false)
                    ->withStringBody($imageBody);
                $this->setResponse($response);
            }
            unset($originalHeight, $originalImage, $originalWidth, $width, $height, $size, $ratio, $mime, $x, $y);
        }
    }

    /**
     * Image resize (crop)
     *
     * @param string $folder Folder path
     * @param string $img Image name
     * @param string $dimension Image dimensions (WxH)
     * @param string $img_ext Image extension
     * @return void
     */
    public function imageResize(string $folder, string $img, string $dimension, string $img_ext)
    {
        $imagePath = WWW_ROOT;
        if ($folder) {
            $imagePath .= str_replace('--', DS, urldecode($folder)) . DS;
        }
        $imagePath .= $img . '.' . strtolower($img_ext);
        if (file_exists($imagePath)) {
            $this->_resize($imagePath, $dimension);
        }

        $this->disableAutoRender();
    }

    /**
     * Image resize (crop proportional)
     *
     * @param string $folder Folder path
     * @param string $img Image name
     * @param string $dimension Image dimensions (WxH)
     * @param string $img_ext Image extension
     * @return void
     */
    public function imageResizeProportional(string $folder, string $img, string $dimension, string $img_ext)
    {
        $imagePath = WWW_ROOT;
        if ($folder) {
            $imagePath .= str_replace('--', DS, urldecode($folder)) . DS;
        }
        $imagePath .= $img . '.' . strtolower($img_ext);
        if (file_exists($imagePath)) {
            $this->_resize($imagePath, $dimension, true);
        }

        $this->disableAutoRender();
    }
}
