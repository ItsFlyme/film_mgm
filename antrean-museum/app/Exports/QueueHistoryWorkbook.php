<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use RuntimeException;
use ZipArchive;

final class QueueHistoryWorkbook
{
    public static function create(Collection $queues): string
    {
        $path = tempnam(storage_path('app'), 'queue-history-');

        if ($path === false) {
            throw new RuntimeException('Tidak dapat membuat file ekspor sementara.');
        }

        $zip = new ZipArchive;
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            unlink($path);
            throw new RuntimeException('Tidak dapat membuat workbook Excel.');
        }

        $headers = ['No. Antrean', 'Nama Perwakilan', 'Nama Instansi', 'Kategori', 'Judul Film', 'Sesi', 'Tanggal', 'Status'];
        $rows = [self::row($headers, 1, 1)];

        $rowNumber = 2;
        foreach ($queues as $queue) {
            $rows[] = self::row([
                $queue->queue_number,
                $queue->visitor_name,
                $queue->institution_name ?? '-',
                $queue->visitor_type === 'group' ? 'Rombongan' : 'Individu',
                $queue->movie_title,
                $queue->session_time,
                $queue->created_at?->copy()->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
                self::statusLabel($queue->status),
            ], 0, $rowNumber++);
        }

        $lastRow = count($rows);
        $zip->addFromString('[Content_Types].xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>
XML);
        $zip->addFromString('_rels/.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>
XML);
        $zip->addFromString('xl/workbook.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets><sheet name="Riwayat 30 Hari" sheetId="1" r:id="rId1"/></sheets>
</workbook>
XML);
        $zip->addFromString('xl/_rels/workbook.xml.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>
XML);
        $zip->addFromString('xl/styles.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="11"/><name val="Calibri"/></font></fonts>
  <fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF1F4E78"/><bgColor indexed="64"/></patternFill></fill></fills>
  <borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>
  <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
  <cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/></cellXfs>
  <cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>
</styleSheet>
XML);
        $worksheet = sprintf(
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<dimension ref="A1:H%d"/><sheetViews><sheetView workbookViewId="0">'
            .'<pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/>'
            .'</sheetView></sheetViews><cols>'
            .'<col min="1" max="1" width="16" customWidth="1"/>'
            .'<col min="2" max="3" width="28" customWidth="1"/>'
            .'<col min="4" max="4" width="16" customWidth="1"/>'
            .'<col min="5" max="5" width="30" customWidth="1"/>'
            .'<col min="6" max="8" width="20" customWidth="1"/>'
            .'</cols><sheetData>%s</sheetData><autoFilter ref="A1:H%d"/>'
            .'<pageMargins left="0.25" right="0.25" top="0.5" bottom="0.5" header="0.2" footer="0.2"/>'
            .'</worksheet>',
            $lastRow,
            implode('', $rows),
            $lastRow
        );
        $zip->addFromString('xl/worksheets/sheet1.xml', $worksheet);

        if (! $zip->close()) {
            unlink($path);
            throw new RuntimeException('Tidak dapat menyimpan workbook Excel.');
        }

        return $path;
    }

    private static function row(array $values, int $style, int $rowNumber): string
    {
        static $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        $cells = '';
        foreach ($values as $index => $value) {
            $cells .= sprintf(
                '<c r="%s%d" t="inlineStr" s="%d"><is><t xml:space="preserve">%s</t></is></c>',
                $columns[$index],
                $rowNumber,
                $style,
                htmlspecialchars((string) $value, ENT_QUOTES | ENT_XML1, 'UTF-8')
            );
        }

        return sprintf('<row r="%d">%s</row>', $rowNumber, $cells);
    }

    private static function statusLabel(string $status): string
    {
        return match ($status) {
            'waiting' => 'Menunggu',
            'called' => 'Dipanggil',
            'completed' => 'Selesai',
            'skipped' => 'Dilewati',
            default => ucfirst($status),
        };
    }
}
