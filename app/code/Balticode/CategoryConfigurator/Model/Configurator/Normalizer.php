<?php

namespace Balticode\CategoryConfigurator\Model\Configurator;

class Normalizer
{
    /**
     * @var ImageUrlProvider
     */
    protected $imageUrlProvider;

    /**
     * @var ImageInfo
     */
    protected $imageInfo;

    /**
     * @param ImageUrlProvider $imageUrlProvider
     * @param ImageInfo $imageInfo
     */
    public function __construct(ImageUrlProvider $imageUrlProvider, ImageInfo $imageInfo)
    {
        $this->imageUrlProvider = $imageUrlProvider;
        $this->imageInfo = $imageInfo;
    }

    /**
     * @param string $imageName
     * @return array
     */
    public function getNormalizedImageData($imageName)
    {
        $normalizedImageData = [
            'name' => $imageName,
            'url' => $this->imageUrlProvider->getImageUrl($imageName)
        ];

        $imageInfo = $this->imageInfo->getImageInfo($imageName);

        if (isset($imageInfo['size'])) {
            $normalizedImageData['size'] = $imageInfo['size'];
        }

        return [ $normalizedImageData ];
    }
}