<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

/**
 * Automatic index.html generation with twig.
 */
class TwigToHtml extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:twig-gen';

    // Create a private variable to store the twig environment
    private $twig;

    // Provide access to service container.
    private $parameterBag;

    public function __construct(Environment $twig, ParameterBagInterface $parameterBag)
    {
        // Inject it in the constructor and update the value on the class
        $this->parameterBag = $parameterBag;
        $this->twig = $twig;

        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'The twig file to export.');

        /*$this->addOption(
            'path',
            '-p',
            InputOption::VALUE_OPTIONAL,
            'Output html path',
            false
        );*/
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //optionPath = $input->getOption('-p');

        // Load Twig File
        $template = $this->twig->load($input->getArgument('file'));

        // Render HTML
        $html = $template->render([
            'someVariable' => 123,
        ]);

        // Preview HTML in the terminal
        $output->writeln($html);

        // Save generated index a public directory.
        $publicPath = isset($optionPath) ? $optionPath : $this->parameterBag->get('kernel.project_dir').'/public/'.'index.html';

        file_put_contents($publicPath, $html, LOCK_EX);

        return 0;
    }
}
