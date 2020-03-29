<?php

namespace Amethyst\Actions;

use Amethyst\Managers\FileManager;
use Amethyst\Services\Bag;
use Illuminate\Support\Facades\Config;
use Railken\Template\Generators;

class Export
{
    public function export(Bag $data, $arguments)
    {
        $filename = $data->get('filename');

        $entity = app('amethyst')->get($data->data)->newEntity();
        $query = $entity->filter($data->filter);

        // Overwrite filename if driver is local
        $diskName = Config::get('medialibrary.disk_name');

        if (Config::get("filesystems.disks.$diskName.driver", 'local') === 'local') {
            $filename = bin2hex(random_bytes(32)).'-'.$filename;
        }

        $filename = sys_get_temp_dir().'/'.$filename;

        $writer = $this->newWriter($filename);

        $body = $arguments->body;

        $row = array_values((array) $body);

        if ($this->shouldWriteHead()) {
            $this->write($writer, array_keys((array) $body));
        }

        $generator = new Generators\TextGenerator();
        $genFile = $generator->generateViewFile(strval(json_encode($row)));

        $query->chunk(100, function ($resources) use ($writer, $genFile, $generator, $data) {
            foreach ($resources as $resource) {
                $encoded = strval($generator->render($genFile, array_merge($data->toArray(), [
                    'resource' => $resource,
                ])));

                $encoded = preg_replace('/\t+/', '\\\\t', strval($encoded));
                $encoded = preg_replace('/\n+/', '\\\\n', strval($encoded));
                $encoded = preg_replace('/\r+/', '\\\\r', strval($encoded));

                $value = json_decode(strval($encoded), true);

                if ($value === null) {
                    throw new \Exception(sprintf('Error while formatting resource #%s', $resource->id));
                }

                $this->write($writer, $value);
            }
        });

        $fm = new FileManager();
        $this->save($writer);
        $result = $fm->create([]);
        $resource = $result->getResource();
        $resource
            ->addMedia($filename)
            ->addCustomHeaders([
                'ContentDisposition' => 'attachment; filename='.basename($filename).'',
                'ContentType'        => $this->getMimeType(),
            ])
            ->toMediaCollection('exporter');

        $data->set('file', $resource->getFullUrl());
    }

    public function getMimeType()
    {
        return 'text/plain';
    }

    public function newWriter($filename)
    {
        return fopen($filename, 'w');
    }

    public function write($writer, $value)
    {
        fwrite($writer, implode(',', $value));
    }

    public function shouldWriteHead()
    {
        return true;
    }

    public function save($writer)
    {
        fclose($writer);
    }
}
