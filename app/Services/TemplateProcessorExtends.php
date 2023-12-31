<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

class TemplateProcessorExtends extends TemplateProcessor
{


    /**
     * Clone a block.
     *
     * @param string $blockname
     * @param int $clones How many time the block should be cloned
     * @param bool $replace
     * @param bool $indexVariables If true, any variables inside the block will be indexed (postfixed with #1, #2, ...)
     * @param array $variableReplacements Array containing replacements for macros found inside the block to clone
     *
     * @return string|null
     */
    public function cloneBlock($blockname, $clones = 1, $replace = true, $indexVariables = false, $variableReplacements = null)
    {
        $idx_tag = mb_strpos($this->tempDocumentMainPart, '${' . $blockname . '}');

        if ($idx_tag === false)
            return null;

        $idx_start = mb_strrpos(mb_substr($this->tempDocumentMainPart, 0, $idx_tag), '<w:p ');
        $idx_end   =  mb_strpos($this->tempDocumentMainPart,               '${/' . $blockname . '}', $idx_tag);


        if ($idx_start === false || $idx_end === false)
            return null;

        $idx_end = mb_strpos($this->tempDocumentMainPart, '</w:p>', $idx_end);

        if ($idx_end === false)
            return null;

        $idx_end += 6;

        $what = mb_substr($this->tempDocumentMainPart, $idx_start, $idx_end - $idx_start);

        // --- //

        $idx_content_start =   mb_strpos($what, 'p>');
        $idx_content_end   =  mb_strrpos($what, '<w:p ');

        if ($idx_content_start === false || $idx_content_end === false)
            return null;

        $idx_content_start += 2;

        $xmlBlock = mb_substr($what, $idx_content_start, $idx_content_end - $idx_content_start);

        // --- //

        if ($replace) {
            $by = array();

            if ($indexVariables)
                $by = $this->indexClonedVariables($clones, $xmlBlock);
            elseif ($variableReplacements !== null && is_array($variableReplacements))
                $by = $this->replaceClonedVariables($variableReplacements, $xmlBlock);
            else
                for ($i = 1; $i <= $clones; $i++)
                    $by[] = $xmlBlock;

            $by = implode('', $by);

            $this->tempDocumentMainPart = str_replace($what, $by, $this->tempDocumentMainPart);
        }

        return $xmlBlock;
    }
}