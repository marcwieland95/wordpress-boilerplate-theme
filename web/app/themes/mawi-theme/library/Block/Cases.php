<?php

declare(strict_types=1);

namespace MaWi\Block;

class Cases extends AbstractBlockController implements BlockDataInterface
{
    public function getBlockData()
    {
        return [
            'cases' => $this->prepareMoreCases(),
            'layout' => 'third',
        ];
    }
}
