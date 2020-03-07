<?php

namespace Amethyst\Actions;

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;

class ExportXls extends Export
{
    public function getMimeType()
    {
        return 'application/vnd.ms-excel';
    }

    public function newWriter($filename)
    {
        $writer = WriterFactory::create(Type::XLSX);
        $writer->openToFile($filename);

        return $writer;
    }

    public function write($writer, $value)
    {
        $writer->addRow($value);
    }

    public function shouldWriteHead()
    {
        return true;
    }

    public function save($writer)
    {
        $writer->close();
    }
}
