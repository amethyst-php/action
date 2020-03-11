<?php

namespace Amethyst\Actions;

use Amethyst\Exceptions\FormattingException;
use Amethyst\Models\WorkflowNode;
use Amethyst\Models\WorkflowNodeState;
use Amethyst\Services\Bag;
use Symfony\Component\Yaml\Yaml;

class Exporter extends Action
{
    public function handle(Bag $data, WorkflowNode $workflowNode, WorkflowNodeState $nodeState = null)
    {
        $types = [
            'csv'  => ExportCsv::class,
            'xlsx' => ExportXls::class,
        ];

        $export = new $types[$data->type]();
        $export->export($data, (object) Yaml::parse($workflowNode->arguments));

        /*
        try {


        } catch (FormattingException | \PDOException | \Railken\SQ\Exceptions\QuerySyntaxException $e) {

            // return event(new \Amethyst\Events\ExporterFailed($this->exporter, $e, $this->agent));

        } catch (\Twig_Error $e) {
            $e = new \Exception($e->getRawMessage().' on line '.$e->getTemplateLine());

            // return event(new \Amethyst\Events\ExporterFailed($this->exporter, $e, $this->agent));
        }

        // event(new \Amethyst\Events\ExporterGenerated($this->exporter, $result->getResource(), $this->agent));
        */

        $this->done($data);
    }
}
