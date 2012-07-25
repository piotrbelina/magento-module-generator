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

class GeneratorCommand extends Command
{
	/**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
        	->setDefinition(array(
        		new InputOption('with-write', '', InputOption::VALUE_NONE, 'Whether or not to generate create, new and delete actions'),
    		))
            ->setName('generate:module')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$dialog = $this->getDialogHelper();

        if ($input->isInteractive()) {
            if (!$dialog->askConfirmation($output, $dialog->getQuestion('Do you confirm generation', 'yes', '?'), true)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        $dialog->writeSection($output, 'CRUD generation');
		$output->writeln('Generating the CRUD code: <info>OK</info>');

	 	$errors = array();
        $runner = $dialog->getRunner($output, $errors);

        $dialog->writeGeneratorSummary($output, $errors);

    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Doctrine2 CRUD generator');

        // namespace
        $output->writeln(array(
            '',
            'This command helps you generate CRUD controllers and templates.',
            '',
            'First, you need to give the entity for which you want to generate a CRUD.',
            'You can give an entity that does not exist yet and the wizard will help',
            'you defining it.',
            '',
            'You must use the shortcut notation like <comment>AcmeBlogBundle:Post</comment>.',
            '',
        ));


        // write?
        $withWrite = $input->getOption('with-write') ?: false;
        $output->writeln(array(
            '',
            'By default, the generator creates two actions: list and show.',
            'You can also ask it to generate "write" actions: new, update, and delete.',
            '',
        ));
        $withWrite = $dialog->askConfirmation($output, $dialog->getQuestion('Do you want to generate the "write" actions', $withWrite ? 'yes' : 'no', '?'), $withWrite);
        $input->setOption('with-write', $withWrite);

        // summary
        $output->writeln(array(
            '',
            $this->getHelper('formatter')->formatBlock('Summary before generation', 'bg=blue;fg=white', true),
            '',
            sprintf("You are going to generate a CRUD controller for \"<info>%s:%s</info>\"", 'bundle', '$entity'),
            sprintf("using the \"<info>%s</info>\" format.", '$format'),
            '',
        ));
    }


    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Piotr\Generator\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }

}