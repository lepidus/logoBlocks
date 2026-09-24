<?php

import('lib.pkp.classes.controllers.grid.GridCellProvider');

class SponsorsBlockGridCellProvider extends GridCellProvider
{
    public function getTemplateVarsFromRowColumn($row, $column)
    {
        $logo = $row->getData();
        switch ($column->getId()) {
            case 'image':
                return array('logoImageUrl' => $logo['imageUrl'], 'logoName' => $logo['name']);
            case 'url':
                return array('label' => $logo['url'] ?: __('plugins.block.sponsorsBlock.noLink'));
            default:
                return array('label' => $logo['name']);
        }
    }
}
