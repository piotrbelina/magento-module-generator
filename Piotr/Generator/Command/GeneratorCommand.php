<?php

namespace Piotr\Generator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\NullOutput;

use Piotr\Generator\Command\Helper\DialogHelper;
use Piotr\Generator\Command\Model\Config;

use Piotr\Generator\Generator\ModuleGenerator;

class GeneratorCommand extends Command {
    
    /**
     *
     * @var Config
     */
    protected $config;
    
    /**
     * 
     * @return Config
     */
    public function getConfig() {
        return $this->config;
    }

    public function setConfig(Config $config) {
        $this->config = $config;
        return $this;
    }

    
    /**
     * {@inheritdoc}
     */
    protected function configure() {
        $this
                ->setDefinition(array(
                    new InputOption('namespace', 'ns', InputOption::VALUE_REQUIRED, 'Namespace'),
                    new InputOption('module', 'm', InputOption::VALUE_REQUIRED, 'Module name'),
                    new InputOption('code-pool', 'p', InputOption::VALUE_REQUIRED, 'Code pool (community or local)'),
                    new InputOption('with-block', '', InputOption::VALUE_NONE, 'Whether or not to generate blocks'),
                    new InputOption('with-controllers', '', InputOption::VALUE_NONE, 'Whether or not to generate controller'),
                    new InputOption('with-helper', '', InputOption::VALUE_NONE, 'Whether or not to generate helper'),
                    new InputOption('with-model', '', InputOption::VALUE_NONE, 'Whether or not to generate model'),
                    new InputOption('with-setup', '', InputOption::VALUE_NONE, 'Whether or not to generate setup script'),
                    new InputOption('active', '', InputOption::VALUE_NONE, 'Whether or not to set module active'),
                ))
                ->setName('magento:generate:module')
                ->setAliases(array('mgm'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $dialog = $this->getDialogHelper();

        if ($input->isInteractive()) {
            if (!$dialog->askConfirmation($output, $dialog->getQuestion('Do you confirm generation', 'yes', '?'), true)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        $dialog->writeSection($output, 'Module generation');
        
        $generator = $this->getGenerator();
        $generator->generate($this->getConfig());
        
        $output->writeln('Generating the CRUD code: <info>OK</info>');

        $errors = array();
        $runner = $dialog->getRunner($output, $errors);

        $dialog->writeGeneratorSummary($output, $errors);
    }

    protected function interact(InputInterface $input, OutputInterface $output) {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Magento module generator');

        // namespace
        $output->writeln(array(
            '',
            'This command helps you generate CRUD controllers and templates.',
            '',
        ));

        // namespace?
        $namespace = $dialog->ask($output, 
                $dialog->getQuestion('Namespace', $this->getConfig()->getNamespace(), '?'), $this->getConfig()->getNamespace());
        $input->setOption('namespace', $namespace);
        
        // code pool
        $codePool = $dialog->ask($output, 
                $dialog->getQuestion('Code pool', $this->getConfig()->getCodePool(), '?'), $this->getConfig()->getCodePool());
        $input->setOption('code-pool', $codePool);
        
        // module name
        $module = $dialog->askAndValidate($output, 
                $dialog->getQuestion('Module name', '', '?'), $this->getNameValidator());
        $input->setOption('module', $module);
        
        // block
        $withBlock = $dialog->askConfirmation($output, 
                        $dialog->getQuestion('Generate Block', $this->getConfig()->getWithBlock() ? 'yes' : 'no', '?'),
                        $this->getConfig()->getWithBlock());
        $input->setOption('with-block', $withBlock);
        
        // controllers
        $withControllers = $dialog->askConfirmation($output, 
                        $dialog->getQuestion('Generate controllers', $this->getConfig()->getWithControllers() ? 'yes' : 'no', '?'),
                        $this->getConfig()->getWithControllers());
        $input->setOption('with-controllers', $withControllers);
        
        // helper
        $withHelper = $dialog->askConfirmation($output, 
                        $dialog->getQuestion('Generate Helper', $this->getConfig()->getWithControllers() ? 'yes' : 'no', '?'),
                        $this->getConfig()->getWithControllers());
        $input->setOption('with-helper', $withHelper);
        
        // with-model
        $withModel = $dialog->askConfirmation($output, 
                        $dialog->getQuestion('Generate Model', $this->getConfig()->getWithModel() ? 'yes' : 'no', '?'),
                        $this->getConfig()->getWithModel());
        $input->setOption('with-model', $withModel);
        
        // with-setup
        $withSetup = $dialog->askConfirmation($output, 
                        $dialog->getQuestion('Generate setup', $this->getConfig()->getWithModel() ? 'yes' : 'no', '?'),
                        $this->getConfig()->getWithSetup());
        $input->setOption('with-setup', $withSetup);
        
        // active
        $active = $dialog->askConfirmation($output, 
                        $dialog->getQuestion('Set module active', $this->getConfig()->getWithModel() ? 'yes' : 'no', '?'),
                        $this->getConfig()->getActive());
        $input->setOption('active', $active);
        
        $this->getConfig()->setActive($active);
        $this->getConfig()->setCodePool($codePool);
        $this->getConfig()->setNamespace($namespace);
        $this->getConfig()->setWithBlock($withBlock);
        $this->getConfig()->setWithControllers($withControllers);
        $this->getConfig()->setWithHelper($withHelper);
        $this->getConfig()->setWithModel($withModel);
        $this->getConfig()->setWithSetup($withSetup);

        // summary
        $output->writeln(array(
            '',
            $this->getHelper('formatter')->formatBlock('Summary before generation', 'bg=blue;fg=white', true),
            '',
            sprintf("You are going to generate a Magento Module \"<info>%s_%s</info>\"", $namespace, $module),
            sprintf("in code pool \"<info>%s</info>\" format.", $codePool),
            sprintf("Active <info>%s</info>", $active ? 'yes' : 'no'),
            sprintf("Block <info>%s</info>", $withBlock ? 'yes' : 'no'),
            sprintf("Contollers <info>%s</info>", $withBlock ? 'yes' : 'no'),
            sprintf("Helper <info>%s</info>", $withHelper ? 'yes' : 'no'),
            sprintf("Model <info>%s</info>", $withModel ? 'yes' : 'no'),
            sprintf("Setup <info>%s</info>", $withSetup ? 'yes' : 'no'),
            '',
        ));
    }
    
    protected function getGenerator() {
        return new ModuleGenerator(__DIR__.'/../Resources/skeleton/module');
    }

        /**
     * @return \Closure
     */
    protected function getNameValidator() {
        return function ($name) {
            if (preg_match('/^[a-zA-Z]+$/', $name) == 0) {
                throw new \InvalidArgumentException('Invalid name ' . $name . ' (only letters)');
            }
            return $name;
        };
    }

    protected function getDialogHelper() {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Piotr\Generator\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }

}