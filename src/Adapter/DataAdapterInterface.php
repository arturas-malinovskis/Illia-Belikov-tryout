<?php

declare(strict_types=1);

namespace App\Adapter;

interface DataAdapterInterface
{
    /**
     * Convert input data to array separated by weeks of year
     *
     * @return array
     */
    public function convert(): array;
}
