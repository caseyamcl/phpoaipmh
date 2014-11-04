<?php

/**
 * PHPOAIPMH Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/phpoaipmh
 * @version 2.0
 * @package caseyamcl/phpoaipmh
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @author Matthias Vandermaesen <matthias.vandermaesen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace Phpoaipmh\Example;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

/**
 * A symfony 2 console command.
 *
 * This command does these things:
 *
 *  - Return the basic information from the nsdl.org OAI endpoint.
 *  - Return a list of available formats from the nsdl.org OAI endpoint.
 *  - Return a list of the first 10 records of the first available format
 *    on the nsdl.org OAI endpoint.
 *  - Throws an example exception from a non existing format.
 *
 * @since v1.0
 * @author Matthias Vandermaesen <matthias.vandermaesen@gmail.com>
 */
class ExampleCommand extends Command
{
    /**
     * @var Example
     */
    private $example;

    /**
     * Constructor
     *
     * @param Example $example An istance of type Example. Injected as a service
     *                         by the container.
     */
    public function __construct(Example $example)
    {
      $this->example = $example;
      parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("phpoaipmh:example")
            ->addOption('call', null, InputOption::VALUE_OPTIONAL, '', 'all')
            ->setDescription("Example implementation of the OAI-PMH library.");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = $this->getHelper('formatter');
        $call = $input->getOption('call');

        if ($call == 'basic' || $call == 'all') {
            $data = $this->example->getBasicInformation();
            $output->writeln("Basic information: ");
            foreach ($data as $key => $value) {
              $line = $formatter->formatSection($key, $value);
              $output->writeln($line);
            }
        }

        if ($call == 'formats' || $call == 'all') {
            $data = $this->example->getAvailableMetadataFormats();
            $table = new Table($output);
            $output->writeln("Metadata formats: ");

            $table->setHeaders($data['header']);
            $table->setRows($data['rows']);
            $table->render();
        }

        if ($call == 'records' || $call == 'all') {
            $data = $this->example->getRecords();
            $table = new Table($output);
            $output->writeln("Ten records: ");

            $table->setHeaders($data['header']);
            $table->setRows($data['rows']);
            $table->render();
        }

        if ($call == 'exception' || $call == 'all') {
            $data = $this->example->tryAnException();
        }
    }
}
