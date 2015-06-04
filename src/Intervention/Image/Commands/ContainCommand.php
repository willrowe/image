<?php

namespace Intervention\Image\Commands;

use \Closure;

class ContainCommand extends \Intervention\Image\Commands\AbstractCommand
{
    /**
     * Draws rectangle on given image
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $width = $this->argument(0)->type('digit')->required()->value();
        $height = $this->argument(1)->type('digit')->required()->value();
        $resizeCanvas = $this->argument(2)->type('boolean')->value();
        $fill = $this->argument(3)->value();

        // Resize
        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });

        // Resize canvas and apply fill color if applicable
        if ($resizeCanvas) {
            $image->resizeCanvas($width, $height, 'center', false, $this->resolveBackgroundColor($image, $fill));
        }

        return true;
    }

    /**
     * Determines the background color to use.
     * If fill is set to true the color average is used.
     * @param  \Intervention\Image\Image  $image
     * @param mixed $fill
     * @return mixed
     */
    protected function resolveBackgroundColor($image, $fill)
    {
        // Compute the color average
        if ($fill === true) {
            return $this->getColorAverage($image);
        }

        // Use default/transparent background
        if (is_null($fill) || $fill === false) {
            return null;
        }

        // Use set color value
        return $fill;
    }

    /**
     * Averages the colors in the image.
     * @param  \Intervention\Image\Image $image The source image.
     * @return int The color average.
     */
    private function getColorAverage($image)
    {
        $image = clone $image;
        // Reduce to single color and then sample
        $color = $image->limitColors(1)->pickColor(0, 0);
        $image->destroy();

        return $color;
    }
}
