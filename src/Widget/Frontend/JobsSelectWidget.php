<?php

declare(strict_types=1);

namespace Dvc\ContaoPlentaJobsSelectWidgetBundle\Widget\Frontend;

use Contao\StringUtil;
use Contao\Widget;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel as OfferModel;

class JobsSelectWidget extends Widget
{
    public const NAME = 'jobs_select';

    protected $blnSubmitInput = true;
    protected $blnForAttribute = true;
    protected $strTemplate = 'form_job_select';

    public function __construct($arrAttributes = null)
    {
        parent::__construct($arrAttributes);

        $this->arrOptions = array_merge($this->getJobOptions(), $this->arrOptions ?? []);
    }

    public function generate(): string
    {
        $strOptions = '';

        foreach ($this->arrOptions as $arrOption) {
            if ($arrOption['type'] === 'group_start') {
                $strOptions .= sprintf(
                    '<optgroup label="%s">',
                    StringUtil::specialchars($arrOption['label'])
                );
                continue;
            }

            if ($arrOption['type'] === 'group_end') {
                $strOptions .= '</optgroup>';
                continue;
            }

            $strOptions .= sprintf(
                '<option value="%s"%s>%s</option>',
                StringUtil::specialchars($arrOption['value']),
                $this->isSelected($arrOption) ? ' selected' : '',
                $arrOption['label']
            );
        }

        return sprintf(
            '<select name="%s" id="ctrl_%s" class="select%s"%s>%s</select>',
            $this->strName,
            $this->strId,
            ($this->strClass ? ' ' . $this->strClass : ''),
            $this->getAttributes(),
            $strOptions
        );
    }

    protected function isSelected($arrOption)
    {
        if (empty($this->varValue) && empty($_POST) && ($arrOption['default'] ?? false)) {
            return true;
        }

        return $arrOption['value'] === $this->varValue;
    }

    public function getOptions(): array
    {
        $options = [];

        foreach ($this->arrOptions as $option) {
            if ($option['type'] === 'group_start') {
                $options[] = [
                    'type' => 'group_start',
                    'label' => $option['label'] ?? '',
                ];
                continue;
            }

            if ($option['type'] === 'group_end') {
                $options[] = [
                    'type' => 'group_end',
                ];
                continue;
            }

            $options[] = [
                'type' => 'option',
                'label' => $option['label'] ?? '',
                'value' => $option['value'] ?? '',
                'selected' => $this->isSelected($option) ? ' selected' : '',
            ];
        }

        return $options;
    }

    private function getJobOptions(): array
    {
        $publishedOffers = OfferModel::findAllPublishedByTypesAndLocation([], []);

        if ($publishedOffers === null) {
            return [];
        }

        return array_map(fn($offer) => [
            'type' => 'option',
            'label' => self::removeBasicEntities($offer->title),
            'value' => self::removeBasicEntities($offer->title),
        ], $publishedOffers->getModels());
    }

    private static function removeBasicEntities(string $source): string
    {
        $source = StringUtil::restoreBasicEntities($source);
        $source = StringUtil::decodeEntities($source);

        return str_replace(['&amp;', '&lt;', '&gt;', '&nbsp;', '&shy;', '&ZeroWidthSpace;'], ['&', '<', '>', ' ', '', ''], $source);
    }
}
