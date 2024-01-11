<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;

class ImageProcessor
{
    const MEDIA_PATH = 'amasty/bannerslider';

    const MEDIA_TMP_PATH = 'amasty/bannerslider/tmp';

    /**
     * @var \Magento\Catalog\Model\ImageUploader
     */
    private $imageUploader;

    /**
     * @var \Magento\Framework\ImageFactory
     */
    private $imageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $ioFile;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\ImageUploader $imageUploader,
        \Magento\Framework\ImageFactory $imageFactory,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->imageUploader = $imageUploader;
        $this->imageFactory = $imageFactory;
        $this->storeManager = $storeManager;
        $this->ioFile = $ioFile;
        $this->logger = $logger;
    }

    /**
     * @param $src
     * @param int $width
     * @param int $height
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getResizedUrl(
        $src,
        $width = 200,
        $height = 200
    ) {
        $dir = self::MEDIA_PATH;
        $absPath = $this->getAbsolutePath($dir);
        $absPath .=  '/' . $src;
        if (!$absPath || !$this->ioFile->fileExists($absPath)) {
            return '';
        }

        $imageResized = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath($dir) .
            $this->getNewDirectoryImage($src, $width, $height);

        $imageResize = $this->imageFactory->create(['fileName' => $absPath]);

        $imageResize->open();
        $imageResize->backgroundColor([255, 255, 255]);
        $imageResize->constrainOnly(true);
        $imageResize->keepTransparency(true);
        $imageResize->keepFrame(true);
        $imageResize->keepAspectRatio(true);

        $imageResize->resize($width, $height);
        $imageResize->save($imageResized);
        $resizedURL = $this->storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .
            $dir . $this->getNewDirectoryImage($src, $width, $height);

        return $resizedURL;
    }

    /**
     * @param $dir
     *
     * @return string
     */
    private function getAbsolutePath($dir)
    {
        $path = '';
        if ($dir) {
            $path = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath($dir);
        }

        return  $path;
    }

    /**
     * @param $src
     * @param $width
     * @param $height
     * @return string
     */
    public function getNewDirectoryImage($src, $width, $height)
    {
        $segments = array_reverse(explode('/', $src));
        $first_dir = substr($segments[0], 0, 1);
        $second_dir = substr($segments[0], 1, 1);

        return '/cache/' . $first_dir . '/' . $second_dir . '/' . $width . '/' . $height . '/' . $segments[0];
    }

    /**
     * @return \Magento\Framework\Filesystem\Directory\WriteInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }

    /**
     * @param $imageName
     * @return string
     */
    public function getThumbnailUrl($imageName)
    {
        try {
            return $this->getImageMediaUrl(self::MEDIA_PATH) . '/' . $imageName;
        } catch (NoSuchEntityException $exception) {
            return '';
        }
    }

    /**
     * @param string $iconName
     *
     * @return string
     */
    private function getImageRelativePath($iconName)
    {
        return self::MEDIA_PATH . DIRECTORY_SEPARATOR . $iconName;
    }

    /**
     * @param string $iconName
     *
     * @return string
     */
    private function getImageRelativeTmpPath($iconName)
    {
        return self::MEDIA_TMP_PATH . DIRECTORY_SEPARATOR . $iconName;
    }

    /**
     * @param $mediaPath
     * @return string
     * @throws NoSuchEntityException
     */
    private function getImageMediaUrl($mediaPath)
    {
        return $this->storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $mediaPath;
    }

    /**
     * @param string $iconName
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveImage(string $iconName): string
    {
        try {
            $path = $this->imageUploader->moveFileFromTmp($iconName, true);
            $path = explode('/', $path);
            $iconName = end($path);

            $filename = $this->getMediaDirectory()->getAbsolutePath($this->getImageRelativePath($iconName));

            /** @var \Magento\Framework\Image $imageProcessor */
            $imageProcessor = $this->imageFactory->create(['fileName' => $filename]);
            $imageProcessor->keepAspectRatio(true);
            $imageProcessor->keepFrame(true);
            $imageProcessor->keepTransparency(true);
            $imageProcessor->backgroundColor([255, 255, 255]);
            $imageProcessor->save();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }

        return $iconName;
    }

    /**
     * @param string $iconName
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function deleteImage(string $iconName)
    {
        $this->getMediaDirectory()->delete($this->getImageRelativePath($iconName));
    }

    /**
     * @param $imageName
     *
     * @return array|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function copy($imageName)
    {
        $basePath = $this->getMediaDirectory()->getAbsolutePath($this->getImageRelativePath($imageName));
        $counter = 1;
        $origName = $imageName;

        do {
            $imageName = explode('.', $origName);
            $imageName[0] .= '-' . ($counter++);
            $imageName = implode('.', $imageName);
            $newPath = $this->getMediaDirectory()->getAbsolutePath($this->getImageRelativePath($imageName));
        } while ($this->ioFile->fileExists($newPath));

        try {
            $this->ioFile->cp(
                $basePath,
                $newPath
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }

        return $imageName;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getAllImages(): array
    {
        $path = $this->getMediaDirectory()->getAbsolutePath() . self::MEDIA_PATH . DIRECTORY_SEPARATOR;
        foreach ($this->getMediaDirectory()->getDriver()->readDirectory($path) as $image) {
            $images[] = substr($image, strrpos($image, '/') + 1);
        }

        return $images ?? [];
    }
}
