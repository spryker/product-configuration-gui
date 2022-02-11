<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductConfigurationGui\Communication\Expander;

use Spryker\Zed\ProductConfigurationGui\Persistence\ProductConfigurationGuiRepositoryInterface;

class ProductConfigurationTableDataExpander implements ProductConfigurationTableDataExpanderInterface
{
    /**
     * @uses \Spryker\Zed\ProductManagement\Communication\Table\ProductTable::COL_PRODUCT_TYPES
     *
     * @var string
     */
    protected const COL_PRODUCT_TYPES = 'product_types';

    /**
     * @uses \Spryker\Zed\ProductManagement\Communication\Table\ProductTable::COL_SKU
     *
     * @var string
     */
    protected const COL_SKU = 'sku';

    /**
     * @var string
     */
    protected const CONFIGURABLE_PRODUCT_TYPE = 'Configurable Product';

    /**
     * @var \Spryker\Zed\ProductConfigurationGui\Persistence\ProductConfigurationGuiRepositoryInterface
     */
    protected $productConfigurationGuiRepository;

    /**
     * @param \Spryker\Zed\ProductConfigurationGui\Persistence\ProductConfigurationGuiRepositoryInterface $productConfigurationGuiRepository
     */
    public function __construct(ProductConfigurationGuiRepositoryInterface $productConfigurationGuiRepository)
    {
        $this->productConfigurationGuiRepository = $productConfigurationGuiRepository;
    }

    /**
     * @param array $item
     *
     * @return array
     */
    public function expandProductItemWithProductConfigurationType(array $item): array
    {
        if (empty($item[static::COL_SKU])) {
            return $item;
        }

        $productConfigurationAggregationTransfer = $this->productConfigurationGuiRepository
            ->findProductConfigurationAggregation($item[static::COL_SKU]);

        if (!$productConfigurationAggregationTransfer) {
            return $item;
        }

        if (!$productConfigurationAggregationTransfer->getProductConfigurationCount()) {
            return $item;
        }

        if (
            $productConfigurationAggregationTransfer->getProductConcreteCount()
            === $productConfigurationAggregationTransfer->getProductConfigurationCount()
        ) {
            $item[static::COL_PRODUCT_TYPES] = static::CONFIGURABLE_PRODUCT_TYPE;

            return $item;
        }

        $item[static::COL_PRODUCT_TYPES] = sprintf(
            '%s, %s',
            $item[static::COL_PRODUCT_TYPES],
            static::CONFIGURABLE_PRODUCT_TYPE,
        );

        return $item;
    }

    /**
     * @param array<array<string, mixed>> $items
     * @param array<array<string, mixed>> $productData
     *
     * @return array<array<string, mixed>>
     */
    public function expandProductItemsWithProductData(array $items, array $productData): array
    {
        foreach ($items as $key => $item) {
            $items[$key] = $this->expandProductItemWithProductConfigurationType($item);
        }

        return $items;
    }
}
