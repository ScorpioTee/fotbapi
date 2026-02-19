<?php

namespace App\Service;

use ApiPlatform\Metadata\Exception\RuntimeException;

class FacrParserService
{
    private const COMPETITION_SUBJECT_URL_PATTERN = 'https://www.fotbal.cz/souteze/subjekty/subjekt/%s';
    private const TABLE_URL_PATTERN =  'https://is1.fotbal.cz/souteze/tabulky-souteze.aspx?req=%s&sport=fotbal';

    private ?\DOMDocument $dom = null;
    private array $data;


    public function generateCompetitionData(): void
    {
        $subjectId = 248;

        $this->initDomDocument(sprintf(self::COMPETITION_SUBJECT_URL_PATTERN, $subjectId));
        $this->findCompetitionUUID();
    }

    public function competitionTable(string $uuid): void
    {
        $this->initDomDocument(sprintf(self::TABLE_URL_PATTERN, $uuid));
        $this->fillCompetitionTableData($uuid);
    }

    private function fillCompetitionTableData(string $uuid): void
    {
        $table = $this->dom->getElementsByTagName('table');
        $tableItem = $table->item(0);
        if ($tableItem === null) {
            throw new RuntimeException('Table not published OR parse error.');
            //echo "Table not published ...";
        }
        $rows = $tableItem->getElementsByTagName('tr');
        /** @var DOMElement $row */
        foreach($rows as $row) {
            if (strpos($row->nodeValue, 'Rk.') !== false) continue;

            $col = $row->getElementsByTagName('td');
            $team = trim(preg_replace('/\([0-9]+\)/','',$col->item(1)->nodeValue));
            $scoreArray = explode(':', $col->item(6)->nodeValue);
            $this->data[$uuid] = [
                'position' => $col->item(0)->nodeValue,
                'club' => trim($team),
                'win' => $col->item(3)->nodeValue,
                'draw' => $col->item(4)->nodeValue,
                'lost' => $col->item(5)->nodeValue,
                'goalsScored' => $scoreArray[0],
                'goalsReceived' => $scoreArray[1],
                'points' => $col->item(7)->nodeValue,
            ];
        }
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
        /** @var DOMElement $row */
        foreach($rows as $row) {
            if (strpos($row->nodeValue, 'Soutěž') !== false) continue;
            /** @var \DomElement $anchor */
            $anchor = $row->getElementsByTagName('a')->item(0);
            $hrefItems = explode('/', $anchor->getAttribute('href'));
            $this->data[$hrefItems[count($hrefItems)-1]] = ['name' => $anchor->nodeValue];
        }
    }

}
