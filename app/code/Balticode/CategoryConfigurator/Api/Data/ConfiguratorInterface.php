<?php

namespace Balticode\CategoryConfigurator\Api\Data;

interface ConfiguratorInterface
{
    const CONFIGURATOR_ID = 'configurator_id';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const ENABLE = 'enable';
    const CATEGORY_ID = 'category_id';
    const IMAGE_NAME = 'image_name';

    /**
     * @return string|null
     */
    public function getConfiguratorId();

    /**
     * @param string $configuratorId
     * @return ConfiguratorInterface
     */
    public function setConfiguratorId($configuratorId);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return ConfiguratorInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return ConfiguratorInterface
     */
    public function setDescription($description);

    /**
     * @return bool
     */
    public function getEnable();

    /**
     * @param bool $enable
     * @return ConfiguratorInterface
     */
    public function setEnable($enable);

    /**
     * @return int
     */
    public function getCategoryId();

    /**
     * @param int $categoryId
     * @return ConfiguratorInterface
     */
    public function setCategoryId($categoryId);

    /**
     * @return string
     */
    public function getImageName();

    /**
     * @param string $imageName
     * @return ConfiguratorInterface
     */
    public function setImageName($imageName);
}
