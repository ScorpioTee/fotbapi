<?php

namespace App\Service;

class FacrParserService
{
    private const COMPETITION_SUBJECT_URL_PATTERN = 'https://www.fotbal.cz/souteze/subjekty/subjekt/%s';
    private const TABLE_URL_PATTERN =  'https://is1.fotbal.cz/souteze/tabulky-souteze.aspx?req=%s&sport=fotbal';

    private ?\DOMDocument $dom = null;
    private array $data;


    public function generateCompleteCompetitionData(): void
    {
        $subjectId = 248;

        $this->initDomDocument(sprintf(self::COMPETITION_SUBJECT_URL_PATTERN, $subjectId));
        $this->findCompetitionUUID();
    }

    public function getData(): array
    {
        return $this->data;
    }


    private function initDomDocument($url): void
    {
        $dom = new \DomDocument();
        $content = file_get_contents($url);
        try {
            libxml_use_internal_errors(true);
            $dom->loadHTML($content);
        } catch(\Exception $e) {
            return;
        }
        if ($dom === null) {
            return;
        }
        $this->dom = $dom;
    }



    private function findCompetitionUUID()
    {
        $table = $this->dom->getElementsByTagName('table');
        $tableItem = $table->item(0);
        $rows = $tableItem->getElementsByTagName('tr');
        foreach($rows as $row) {
            if (strpos($row->nodeValue, 'Soutěž') !== false) continue;
            /** @var \DomElement $anchor */
            $anchor = $row->getElementsByTagName('a')->item(0);
            $hrefItems = explode('/', $anchor->getAttribute('href'));
            $this->data[$hrefItems[count($hrefItems)-1]] = ['name' => $anchor->nodeValue];
        }
    }


}
